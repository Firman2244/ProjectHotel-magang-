<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SubmitFinalReportRequest;
use App\Models\Task;
use App\Models\Report;
use App\Models\ReportItem;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\PointHistory;
use App\Jobs\ProcessReportImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportController extends Controller
{
    private const TODO_DEADLINE_HOURS = [1 => 7, 2 => 13, 3 => 22];
    private const SUBMIT_DEADLINE_HOURS = [1 => 15, 2 => 21, 3 => 6];

    public function create()
    {
        $user = Auth::user();

        if (empty($user->shift_id)) {
            return redirect()->route('dashboard')->with('error', 'Jadwal shift Anda belum diatur. Silakan hubungi Administrator.');
        }

        $now = Carbon::now('Asia/Jakarta');
        $reportDate = $this->resolveReportDate((int) $user->shift_id, $now);

        $existingPlanned = Report::where('user_id', $user->id)
            ->where('shift_id', $user->shift_id)
            ->where('report_date', $reportDate)
            ->where('status', 'planned')
            ->first();

        if ($existingPlanned) {
            return redirect()->route('reports.show', $existingPlanned->id)
                ->with('success', 'Anda sudah memiliki todo list aktif, silakan lanjutkan.');
        }

        $hasBaseReport = Report::where('user_id', $user->id)
            ->where('shift_id', $user->shift_id)
            ->where('report_date', $reportDate)
            ->exists();

        $showDoubleShiftForm = $user->hasActiveDoubleShiftPermit($now) && $hasBaseReport;

        return view('reports.create', [
            'user' => $user,
            'tasks' => Task::where('department', $user->department)->get(),
            'showDoubleShiftForm' => $showDoubleShiftForm
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (empty($user->shift_id)) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Jadwal shift Anda belum diatur.');
        }

        $request->validate([
            'shift_id' => 'required|integer|in:1,2,3',
            'items.*.task_id' => 'required|exists:tasks,id',
            'items.*.is_additional' => 'required|boolean'
        ]);

        $now = Carbon::now('Asia/Jakarta');
        $requestedShiftId = (int) $request->input('shift_id');
        $isDoubleShiftAttempt = $requestedShiftId !== (int) $user->shift_id;

        if ($isDoubleShiftAttempt && !$user->hasActiveDoubleShiftPermit($now)) {
            return redirect()->back()->with('error', 'Akses ditolak. Tiket lembur Anda tidak valid atau sudah kedaluwarsa.');
        }

        $reportDate = $this->resolveReportDate($requestedShiftId, $now);
        $deadline = Carbon::parse($reportDate, 'Asia/Jakarta')
            ->setHour(self::TODO_DEADLINE_HOURS[$requestedShiftId] ?? 24)
            ->setMinute(30);

        $isLate = $isDoubleShiftAttempt ? false : $now->greaterThan($deadline);

        DB::beginTransaction();
        try {
            $report = Report::create([
                'user_id' => $user->id,
                'hotel_id' => $user->hotel_id,
                'shift_id' => $requestedShiftId,
                'report_date' => $reportDate,
                'status' => 'planned',
                'is_late' => $isLate,
            ]);

            $inputItems = (array) $request->input('items', []);
            if (!empty($inputItems)) {
                $nowDb = Carbon::now();
                ReportItem::insert(collect($inputItems)->map(fn ($item) => [
                    'report_id' => $report->id,
                    'task_id' => $item['task_id'],
                    'is_additional' => 0,
                    'status' => 'pending',
                    'created_at' => $nowDb,
                    'updated_at' => $nowDb,
                ])->toArray());
            }

            if ($isDoubleShiftAttempt) {
                User::where('id', $user->id)->update([
                    'can_double_shift' => false,
                    'double_shift_date' => null
                ]);
            }

            ActivityLog::record($user->id, 'CREATE_REPORT', "Membuat Todo List untuk tanggal $reportDate pada Shift $requestedShiftId");
            DB::commit();

            return redirect()->route('dashboard')->with('success', $isLate ? 'Todo list dikirim, namun TERLAMBAT!' : 'Todo list dikirim ke admin!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan laporan: ' . $e->getMessage());
        }
    }

    public function updateFinal(SubmitFinalReportRequest $request, Report $report)
    {
        if (Auth::id() !== $report->user_id || $report->status !== 'planned') {
            abort(403);
        }

        $pendingImageJobs = [];

        DB::beginTransaction();
        try {
            $inputItems = (array) $request->input('items', []);

            if (!empty($inputItems)) {
                $existingItems = ReportItem::whereIn('id', array_keys($inputItems))
                    ->where('report_id', $report->id)
                    ->get()
                    ->keyBy('id');

                foreach ($inputItems as $itemId => $itemData) {
                    if ($item = $existingItems->get($itemId)) {
                        $status = $itemData['status'] ?? 'completed';
                        $item->update([
                            'status' => $status,
                            'obstacle_note' => $status === 'pending' ? ($itemData['obstacle_note'] ?? null) : null,
                            'notes' => $itemData['notes'] ?? $item->notes
                        ]);

                        $beforeFile = $request->file("items.{$itemId}.before_image");
                        if ($beforeFile) {
                            $tempPath = $beforeFile->store('reports/tmp', 'public');
                            $pendingImageJobs[] = new ProcessReportImage($item->id, 'before_image', $tempPath, $report->hotel_id);
                        }

                        $afterFile = $request->file("items.{$itemId}.after_image");
                        if ($afterFile) {
                            $tempPath = $afterFile->store('reports/tmp', 'public');
                            $pendingImageJobs[] = new ProcessReportImage($item->id, 'after_image', $tempPath, $report->hotel_id);
                        }
                    }
                }
            }

            $newItems = (array) $request->input('new_items', []);
            if (!empty($newItems)) {
                foreach ($newItems as $index => $newItem) {
                    if (!empty($newItem['custom_task_name'])) {
                        $newItemDb = ReportItem::create([
                            'report_id' => $report->id,
                            'status' => 'completed',
                            'is_additional' => 1,
                            'custom_task_name' => $newItem['custom_task_name'],
                            'notes' => $newItem['notes'] ?? null,
                        ]);

                        $beforeFile = $request->file("new_items.{$index}.before_image");
                        if ($beforeFile) {
                            $tempPath = $beforeFile->store('reports/tmp', 'public');
                            $pendingImageJobs[] = new ProcessReportImage($newItemDb->id, 'before_image', $tempPath, $report->hotel_id);
                        }

                        $afterFile = $request->file("new_items.{$index}.after_image");
                        if ($afterFile) {
                            $tempPath = $afterFile->store('reports/tmp', 'public');
                            $pendingImageJobs[] = new ProcessReportImage($newItemDb->id, 'after_image', $tempPath, $report->hotel_id);
                        }
                    }
                }
            }

            if ($request->input('save_action') === 'draft') {
                ActivityLog::record(Auth::id(), 'DRAFT_REPORT', "Menyimpan draft laporan shift ID: {$report->id}");
                DB::commit();

                foreach ($pendingImageJobs as $job) {
                    dispatch_sync($job);
                }

                return redirect()->back()->with('success', 'Draft berhasil disimpan!');
            }

            $now = Carbon::now('Asia/Jakarta');
            $submitDeadline = Carbon::today('Asia/Jakarta')
                ->setHour(self::SUBMIT_DEADLINE_HOURS[$report->shift_id] ?? 24)
                ->setMinute(30);
            if ($report->shift_id == 3 && $now->hour >= 12) {
                $submitDeadline->addDay();
            }

            $user = Auth::user();
            $isDoubleShiftReport = $report->shift_id !== $user->shift_id;
            $isLateSubmit = $isDoubleShiftReport ? false : $now->greaterThan($submitDeadline);

            $report->update([
                'is_late_submit' => $isLateSubmit,
                'is_late' => $isDoubleShiftReport ? false : $report->is_late,
                'status' => 'completed'
            ]);

            $scores = $this->recalculateReportScore($report);

            PointHistory::create([
                'user_id' => $report->user_id,
                'type' => $isDoubleShiftReport ? 'LEMBUR' : 'HARIAN',
                'description' => 'Laporan Shift ' . $report->shift_id . ' (ID Report: ' . $report->id . ')',
                'points' => $scores['total_score'],
            ]);

            ActivityLog::record($user->id, 'SUBMIT_REPORT', "Menyelesaikan laporan shift ID: {$report->id} dengan skor {$scores['total_score']}");
            DB::commit();

            foreach ($pendingImageJobs as $job) {
                dispatch_sync($job);
            }

            return redirect()->route('dashboard')->with('success', $isLateSubmit ? 'Laporan disubmit, namun TERLAMBAT!' : 'Laporan disubmit! Poin Anda telah ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Submit Report Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses laporan: ' . $e->getMessage());
        }
    }

    public function show(int $id)
    {
        $report = Report::with(['items.task', 'user' => function($query) {
            $query->withTrashed()->select('id', 'name', 'hotel_id', 'department', 'role');
        }])->find($id);

        if (!$report) {
            return redirect()->route('dashboard')->with('error', 'Laporan tersebut sudah dihapus atau tidak ditemukan.');
        }

        $user = Auth::user();

        if ($report->user_id !== $user->id && $user->role !== 'admin') {
            abort(403);
        }

        $hotelId = $report->user ? $report->user->hotel_id : null;

        $prevReport = Report::where('hotel_id', $hotelId)->where('id', '<', $report->id)->orderByDesc('id')->first(['id']);
        $nextReport = Report::where('hotel_id', $hotelId)->where('id', '>', $report->id)->orderBy('id')->first(['id']);
        $totalReports = Report::where('hotel_id', $hotelId)->count();
        $currentIndex = Report::where('hotel_id', $hotelId)->where('id', '<=', $report->id)->count();

        return view('reports.show', compact('report', 'prevReport', 'nextReport', 'totalReports', 'currentIndex'));
    }

    public function history()
    {
        $reports = Report::with('items.task')
            ->where('user_id', Auth::id())
            ->orderByDesc('report_date')
            ->paginate(15);
        return view('reports.history', compact('reports'));
    }

    public function destroy(Report $report)
    {
        $user = Auth::user();

        if ($report->user_id !== $user->id && $user->role !== 'admin') {
            abort(403);
        }

        ActivityLog::record($user->id, 'DELETE_REPORT', "Menghapus laporan shift ID: {$report->id}");
        $this->deleteReportImages($report);

        PointHistory::where('user_id', $report->user_id)
            ->where('description', 'LIKE', '%ID Report: ' . $report->id . '%')
            ->delete();

        $report->delete();

        return redirect()->route($user->role === 'admin' ? 'admin.dashboard' : 'dashboard')->with('success', 'Laporan beserta riwayat poinnya berhasil dihapus.');
    }

    public function destroyItem(ReportItem $item)
    {
        $report = $item->report;

        $user = Auth::user();

        if ($report->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($item->before_image && Storage::disk('public')->exists($item->before_image)) Storage::disk('public')->delete($item->before_image);
        if ($item->after_image && Storage::disk('public')->exists($item->after_image)) Storage::disk('public')->delete($item->after_image);

        $item->delete();

        $isCompleted = $report->status === 'completed';
        $scores = ['total_score' => $report->total_score, 'base_score' => 0, 'bonus_score' => 0, 'penalty' => 0];

        if ($isCompleted) {
            $report->unsetRelation('items');
            $scores = $this->recalculateReportScore($report);
            ActivityLog::record($user->id, 'EDIT_REPORT', "Menghapus tugas dari laporan ID: {$report->id}. Skor diupdate menjadi {$scores['total_score']}");
        }

        return response()->json([
            'success' => true,
            'is_completed' => $isCompleted,
            'new_score' => $scores['total_score'],
            'base_score' => $scores['base_score'],
            'bonus_score' => $scores['bonus_score'],
            'penalty' => $scores['penalty']
        ]);
    }

    public function updateItemStatus(Request $request, ReportItem $item)
    {
        $this->abortUnlessAdmin();

        $request->validate([
            'status' => 'required|in:verified,rejected,completed'
        ]);

        $item->update(['status' => $request->status]);

        $report = $item->report;
        $report->unsetRelation('items');
        $scores = $this->recalculateReportScore($report);

        PointHistory::where('user_id', $report->user_id)
            ->where('description', 'LIKE', '%ID Report: ' . $report->id . '%')
            ->update(['points' => $scores['total_score']]);

        $user = Auth::user();
        ActivityLog::record($user->id, 'VERIFY_TASK', "Admin merubah status tugas ID: {$item->id} menjadi {$request->status}");

        return response()->json([
            'success' => true,
            'new_status' => $request->status,
            'new_score' => $scores['total_score'],
            'base_score' => $scores['base_score'],
            'bonus_score' => $scores['bonus_score'],
            'penalty' => $scores['penalty']
        ]);
    }

    public function verifyAllTasks(Request $request, Report $report)
    {
        $this->abortUnlessAdmin();

        $report->items()->where('status', 'completed')->update(['status' => 'verified']);
        $report->unsetRelation('items');

        $scores = $this->recalculateReportScore($report);

        PointHistory::where('user_id', $report->user_id)
            ->where('description', 'LIKE', '%ID Report: ' . $report->id . '%')
            ->update(['points' => $scores['total_score']]);

        $user = Auth::user();
        ActivityLog::record($user->id, 'VERIFY_ALL', "Admin melakukan Verify All pada laporan ID: {$report->id}");

        return response()->json([
            'success' => true,
            'new_score' => $scores['total_score']
        ]);
    }

    private function resolveReportDate(int $shiftId, Carbon $now): string
    {
        if ($shiftId == 3 && $now->hour < 12) {
            return $now->copy()->subDay()->toDateString();
        }
        return $now->toDateString();
    }

    private function abortUnlessAdmin(): void
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(response()->json(['success' => false, 'message' => 'Unauthorized'], 403));
        }
    }

    private function deleteReportImages(Report $report): void
    {
        $items = ReportItem::where('report_id', $report->id)->get(['before_image', 'after_image']);
        foreach ($items as $item) {
            if ($item->before_image && Storage::disk('public')->exists($item->before_image)) {
                Storage::disk('public')->delete($item->before_image);
            }
            if ($item->after_image && Storage::disk('public')->exists($item->after_image)) {
                Storage::disk('public')->delete($item->after_image);
            }
        }
    }

    private function recalculateReportScore(Report $report): array
    {
        $scores = $report->scoreBreakdown();
        $report->update(['total_score' => $scores['total_score']]);
        return $scores;
    }
}
