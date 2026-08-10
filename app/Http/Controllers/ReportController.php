<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SubmitFinalReportRequest;
use App\Models\Task;
use App\Models\Report;
use App\Models\ReportItem;
use App\Models\ActivityLog;
use App\Jobs\ProcessReportImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        return view('reports.create', ['user' => $user, 'tasks' => Task::where('department', $user->department)->get()]);
    }

    public function store(Request $request)
    {
        $request->validate(['items.*.task_id' => 'required|exists:tasks,id', 'items.*.is_additional' => 'required|boolean']);

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $reportDate = $now->toDateString();

        $deadline = Carbon::today('Asia/Jakarta')->setHour(
            match ($user->shift_id) { 1 => 7, 2 => 13, 3 => 22, default => 24 }
        )->setMinute(30);

        if ($user->shift_id == 3 && $now->hour < 12) {
            $deadline->subDay();
            $reportDate = $now->copy()->subDay()->toDateString();
        }

        $isLate = $now->greaterThan($deadline);

        DB::beginTransaction();
        try {
            $report = Report::create([
                'user_id' => $user->id, 'hotel_id' => $user->hotel_id, 'shift_id' => $user->shift_id,
                'report_date' => $reportDate, 'status' => 'planned', 'is_late' => $isLate,
            ]);

            if ($request->has('items')) {
                $nowDb = Carbon::now();
                ReportItem::insert(collect($request->items)->map(fn($item) => [
                    'report_id' => $report->id, 'task_id' => $item['task_id'], 'is_additional' => 0, 'status' => 'pending', 'created_at' => $nowDb, 'updated_at' => $nowDb,
                ])->toArray());
            }

            ActivityLog::record($user->id, 'CREATE_REPORT', "Membuat Todo List awal shift untuk tanggal $reportDate");
            DB::commit();

            return redirect()->route('dashboard')->with('success', $isLate ? 'Todo list dikirim, namun TERLAMBAT!' : 'Todo list dikirim ke admin!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan laporan.');
        }
    }

    private function handleImageUpload(mixed $file, int $itemId, string $imageType, ?int $hotelId): void
    {
        if (!$file) return;
        $tempPath = $file->store('reports/tmp', 'public');
        $job = new ProcessReportImage($itemId, $imageType, $tempPath, $hotelId);
        app()->environment('local') ? dispatch_sync($job) : dispatch($job);
    }

    private function deleteReportImages(Report $report): void
    {
        $paths = ReportItem::where('report_id', $report->id)->select('before_image', 'after_image')->get()->flatMap(fn($item) => [$item->before_image, $item->after_image])->filter()->unique();
        foreach ($paths as $path) {
            if (Storage::disk('public')->exists($path)) Storage::disk('public')->delete($path);
        }
    }

    public function updateFinal(SubmitFinalReportRequest $request, Report $report)
    {
        if (Auth::id() !== $report->user_id || $report->status !== 'planned') abort(403);

        DB::beginTransaction();
        try {
            if ($request->has('items')) {
                $existingItems = ReportItem::whereIn('id', array_keys($request->items))->where('report_id', $report->id)->get()->keyBy('id');
                foreach ($request->items as $itemId => $itemData) {
                    if ($item = $existingItems->get($itemId)) {
                        $status = $itemData['status'] ?? 'completed';
                        $item->update(['status' => $status, 'obstacle_note' => $status === 'pending' ? ($itemData['obstacle_note'] ?? null) : null, 'notes' => $itemData['notes'] ?? $item->notes]);
                        if (isset($itemData['before_image'])) $this->handleImageUpload($itemData['before_image'], $item->id, 'before_image', $report->hotel_id);
                        if (isset($itemData['after_image'])) $this->handleImageUpload($itemData['after_image'], $item->id, 'after_image', $report->hotel_id);
                    }
                }
            }

            if ($request->has('new_items')) {
                foreach ($request->new_items as $newItem) {
                    if (!empty($newItem['task_name'])) {
                        $newItemDb = ReportItem::create([
                            'report_id' => $report->id, 'status' => 'completed', 'is_additional' => 1,
                            'notes' => "Tugas Tambahan: " . $newItem['task_name'] . (!empty($newItem['notes']) ? " - " . $newItem['notes'] : ""),
                        ]);
                        if (isset($newItem['before_image'])) $this->handleImageUpload($newItem['before_image'], $newItemDb->id, 'before_image', $report->hotel_id);
                        if (isset($newItem['after_image'])) $this->handleImageUpload($newItem['after_image'], $newItemDb->id, 'after_image', $report->hotel_id);
                    }
                }
            }

            if ($request->save_action === 'draft') {
                ActivityLog::record(Auth::id(), 'DRAFT_REPORT', "Menyimpan draft laporan shift ID: {$report->id}");
                DB::commit();
                return redirect()->back()->with('success', 'Draft berhasil disimpan!');
            }

            $now = Carbon::now('Asia/Jakarta');
            $submitDeadline = Carbon::today('Asia/Jakarta')->setHour(match ($report->shift_id) { 1 => 15, 2 => 21, 3 => 6, default => 24 })->setMinute(30);
            if ($report->shift_id == 3 && $now->hour >= 12) $submitDeadline->addDay();

            $isLateSubmit = $now->greaterThan($submitDeadline);

            $standardTasks = $report->items()->where('is_additional', 0)->get();
            $validDenominator = $standardTasks->count() - $standardTasks->where('status', 'pending')->count();

            $baseScore = $validDenominator > 0 ? ($standardTasks->where('status', 'completed')->count() / $validDenominator) * 100 : ($standardTasks->count() > 0 ? 100 : 0);
            $bonusScore = $report->items()->where('is_additional', 1)->where('status', 'completed')->count() * 10;
            $latePenalty = ($report->is_late ? 15 : 0) + ($isLateSubmit ? 15 : 0);

            $finalScore = max(0, (int) round($baseScore + $bonusScore - $latePenalty));

            $report->update(['status' => 'completed', 'total_score' => $finalScore, 'is_late_submit' => $isLateSubmit]);
            ActivityLog::record(Auth::id(), 'SUBMIT_REPORT', "Menyelesaikan laporan shift ID: {$report->id} dengan skor $finalScore");
            DB::commit();

            return redirect()->route('dashboard')->with('success', $isLateSubmit ? 'Laporan disubmit, namun TERLAMBAT!' : 'Laporan disubmit!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Submit Report Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses laporan.');
        }
    }

    public function show(int $id)
    {
        $report = Report::with(['items.task', 'user'])->find($id);

        if (!$report) {
            return redirect()->route('dashboard')->with('error', 'Laporan tersebut sudah dihapus atau tidak ditemukan.');
        }

        if ($report->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $hotelId = $report->user->hotel_id;
        $prevReport = Report::where('hotel_id', $hotelId)->where('id', '<', $report->id)->orderByDesc('id')->first();
        $nextReport = Report::where('hotel_id', $hotelId)->where('id', '>', $report->id)->orderBy('id')->first();

        return view('reports.show', [
            'report' => $report, 'prevReport' => $prevReport, 'nextReport' => $nextReport,
            'totalReports' => Report::where('hotel_id', $hotelId)->count(),
            'currentIndex' => Report::where('hotel_id', $hotelId)->where('id', '<=', $report->id)->count()
        ]);
    }

    public function history()
    {
        return view('reports.history', ['reports' => Report::with('items.task')->where('user_id', Auth::id())->orderByDesc('report_date')->paginate(15)]);
    }

    public function destroy(Report $report)
    {
        if ($report->user_id !== Auth::id() && Auth::user()->role !== 'admin') abort(403);
        ActivityLog::record(Auth::id(), 'DELETE_REPORT', "Menghapus laporan shift ID: {$report->id}");
        $this->deleteReportImages($report);
        $report->delete();

        return redirect()->route(Auth::user()->role === 'admin' ? 'admin.dashboard' : 'dashboard')->with('success', 'Laporan Todo List berhasil dihapus.');
    }

    public function destroyItem(ReportItem $item)
    {
        $report = $item->report;

        if ($report->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($item->before_image && Storage::disk('public')->exists($item->before_image)) Storage::disk('public')->delete($item->before_image);
        if ($item->after_image && Storage::disk('public')->exists($item->after_image)) Storage::disk('public')->delete($item->after_image);

        $item->delete();

        $baseScore = 0; $bonusScore = 0; $latePenalty = 0; $newScore = $report->total_score;
        $isCompleted = $report->status === 'completed';

        if ($isCompleted) {
            $standardTasks = $report->items()->where('is_additional', 0)->get();
            $validDenominator = $standardTasks->count() - $standardTasks->where('status', 'pending')->count();

            $baseScore = $validDenominator > 0 ? ($standardTasks->where('status', 'completed')->count() / $validDenominator) * 100 : ($standardTasks->count() > 0 ? 100 : 0);
            $bonusScore = $report->items()->where('is_additional', 1)->where('status', 'completed')->count() * 10;
            $latePenalty = ($report->is_late ? 15 : 0) + ($report->is_late_submit ? 15 : 0);

            $newScore = max(0, (int) round($baseScore + $bonusScore - $latePenalty));
            $report->update(['total_score' => $newScore]);

            ActivityLog::record(Auth::id(), 'EDIT_REPORT', "Menghapus tugas dari laporan ID: {$report->id}. Skor diupdate menjadi $newScore");
        }

        return response()->json([
            'success' => true,
            'is_completed' => $isCompleted,
            'new_score' => $newScore,
            'base_score' => round($baseScore),
            'bonus_score' => $bonusScore,
            'penalty' => $latePenalty
        ]);
    }
}
