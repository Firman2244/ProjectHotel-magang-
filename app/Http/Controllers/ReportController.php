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
        $tasks = Task::where('department', $user->department)->get();

        return view('reports.create', compact('user', 'tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items.*.task_id'       => 'required|exists:tasks,id',
            'items.*.is_additional' => 'required|boolean',
        ]);

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $shiftId = $user->shift_id;
        $isLate = false;
        $reportDate = $now->toDateString();

        if ($shiftId == 1) {
            $deadline = Carbon::today('Asia/Jakarta')->setHour(7)->setMinute(30);
            $isLate = $now->greaterThan($deadline);
        } elseif ($shiftId == 2) {
            $deadline = Carbon::today('Asia/Jakarta')->setHour(13)->setMinute(30);
            $isLate = $now->greaterThan($deadline);
        } elseif ($shiftId == 3) {
            $deadline = Carbon::today('Asia/Jakarta')->setHour(22)->setMinute(30);
            if ($now->hour < 12) {
                $deadline->subDay();
                $reportDate = $now->copy()->subDay()->toDateString();
            }
            $isLate = $now->greaterThan($deadline);
        }

        DB::beginTransaction();

        try {
            $report = Report::create([
                'user_id'     => $user->id,
                'hotel_id'    => $user->hotel_id,
                'shift_id'    => $user->shift_id,
                'report_date' => $reportDate,
                'status'      => 'planned',
                'is_late'     => $isLate,
            ]);

            if ($request->has('items')) {
                $nowDb = Carbon::now();
                $rows = collect($request->items)->map(function ($item) use ($report, $nowDb) {
                    return [
                        'report_id'     => $report->id,
                        'task_id'       => $item['task_id'],
                        'is_additional' => 0,
                        'status'        => 'pending',
                        'created_at'    => $nowDb,
                        'updated_at'    => $nowDb,
                    ];
                })->toArray();

                ReportItem::insert($rows);
            }

            ActivityLog::record($user->id, 'CREATE_REPORT', "Membuat Todo List awal shift untuk tanggal $reportDate");

            DB::commit();

            $msg = $isLate ? 'Todo list berhasil dikirim, namun tercatat TERLAMBAT!' : 'Todo list berhasil dikirim ke admin!';
            return redirect()->route('dashboard')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan laporan. Silakan coba lagi.');
        }
    }

    /**
     * Upload gambar (before/after) dan proses via job.
     *
     * PENTING: Method ini TIDAK mengembalikan path apapun untuk disimpan manual
     * di controller. Kolom `before_image` / `after_image` pada ReportItem akan
     * di-update SENDIRI oleh job ProcessReportImage setelah gambar selesai
     * di-resize & dipindah ke lokasi final.
     *
     * Sebelumnya method ini me-return $tempPath, lalu path itu dipakai lagi
     * untuk $item->update(['before_image' => $tempPath, ...]) di updateFinal().
     * Ini menyebabkan bug: begitu job selesai (terutama saat dispatchSync di
     * local) dan sudah benar mengisi path final, controller langsung
     * menimpanya lagi dengan path tmp yang sudah dihapus oleh job -> foto
     * jadi broken image walau file aslinya ada di storage.
     */
    private function handleImageUpload(mixed $file, int $itemId, string $imageType, ?int $hotelId): void
    {
        if (!$file) {
            return;
        }

        $tempPath = $file->store('reports/tmp', 'public');

        if (app()->environment('local')) {
            ProcessReportImage::dispatchSync($itemId, $imageType, $tempPath, $hotelId);
        } else {
            ProcessReportImage::dispatch($itemId, $imageType, $tempPath, $hotelId);
        }
    }

    /**
     * Hapus semua file before_image/after_image milik sebuah report dari
     * storage disk 'public'. Dipanggil sebelum $report->delete(), karena
     * cascade DB tidak menghapus file fisik, cuma baris di tabel.
     */
    private function deleteReportImages(Report $report): void
    {
        $items = ReportItem::where('report_id', $report->id)
            ->select('before_image', 'after_image')
            ->get();

        $paths = $items->flatMap(function ($item) {
            return [$item->before_image, $item->after_image];
        })->filter()->unique();

        foreach ($paths as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    public function updateFinal(SubmitFinalReportRequest $request, Report $report)
    {
        if (Auth::id() !== $report->user_id || $report->status !== 'planned') {
            abort(403, 'Anda tidak memiliki akses untuk menyelesaikan laporan ini.');
        }

        $isDraft = $request->save_action === 'draft';
        $completedCount = 0;
        $totalItems = 0;
        $score = 0;
        $hotelId = $report->hotel_id;

        DB::beginTransaction();

        try {
            if ($request->has('items')) {
                $itemIds = array_keys($request->items);
                $existingItems = ReportItem::whereIn('id', $itemIds)
                    ->where('report_id', $report->id)
                    ->get()
                    ->keyBy('id');

                $totalItems = count($request->items);

                foreach ($request->items as $itemId => $itemData) {
                    $item = $existingItems->get($itemId);
                    if ($item) {
                        $status = $itemData['status'] ?? 'completed';
                        $obstacleNote = $status === 'pending' ? ($itemData['obstacle_note'] ?? null) : null;

                        // Update data non-gambar dulu. Kolom before_image/after_image
                        // SENGAJA tidak disentuh di sini supaya tidak menimpa hasil
                        // update dari job ProcessReportImage (lihat handleImageUpload).
                        $item->update([
                            'status'        => $status,
                            'obstacle_note' => $obstacleNote,
                            'notes'         => $itemData['notes'] ?? $item->notes,
                        ]);

                        // Upload gambar baru HANYA jika ada file yang dikirim.
                        // Job yang akan mengisi kolom before_image/after_image
                        // dengan path final setelah selesai diproses.
                        if (isset($itemData['before_image'])) {
                            $this->handleImageUpload($itemData['before_image'], $item->id, 'before_image', $hotelId);
                        }
                        if (isset($itemData['after_image'])) {
                            $this->handleImageUpload($itemData['after_image'], $item->id, 'after_image', $hotelId);
                        }

                        if ($status === 'completed') {
                            $completedCount++;
                        }
                    }
                }
            }

            if ($totalItems > 0) {
                $score = round(($completedCount / $totalItems) * 100);
            }

            if ($request->has('new_items')) {
                foreach ($request->new_items as $newItem) {
                    if (!empty($newItem['task_name'])) {
                        $newItemDb = ReportItem::create([
                            'report_id'     => $report->id,
                            'task_id'       => null,
                            'status'        => 'completed',
                            'before_image'  => null,
                            'after_image'   => null,
                            'notes'         => "Tugas Tambahan: " . $newItem['task_name'] . (!empty($newItem['notes']) ? " - " . $newItem['notes'] : ""),
                            'is_additional' => 1,
                        ]);

                        // Sama seperti di atas: job yang akan mengisi
                        // before_image/after_image dengan path final.
                        if (isset($newItem['before_image'])) {
                            $this->handleImageUpload($newItem['before_image'], $newItemDb->id, 'before_image', $hotelId);
                        }
                        if (isset($newItem['after_image'])) {
                            $this->handleImageUpload($newItem['after_image'], $newItemDb->id, 'after_image', $hotelId);
                        }

                        $score += 15;
                    }
                }
            }

            if ($isDraft) {
                ActivityLog::record(Auth::id(), 'DRAFT_REPORT', "Menyimpan draft laporan shift ID: {$report->id}");
                DB::commit();
                return redirect()->back()->with('success', 'Draft laporan berhasil disimpan! Anda bisa melanjutkannya nanti.');
            }

            $now = Carbon::now('Asia/Jakarta');
            $shiftId = $report->shift_id;
            $isLateSubmit = false;

            if ($shiftId == 1) {
                $submitDeadline = Carbon::today('Asia/Jakarta')->setHour(15)->setMinute(30);
                $isLateSubmit = $now->greaterThan($submitDeadline);
            } elseif ($shiftId == 2) {
                $submitDeadline = Carbon::today('Asia/Jakarta')->setHour(21)->setMinute(30);
                $isLateSubmit = $now->greaterThan($submitDeadline);
            } elseif ($shiftId == 3) {
                $submitDeadline = Carbon::today('Asia/Jakarta')->setHour(6)->setMinute(30);
                if ($now->hour >= 12) {
                    $submitDeadline->addDay();
                }
                $isLateSubmit = $now->greaterThan($submitDeadline);
            }

            if ($report->is_late) {
                $score -= 20;
            }

            if ($isLateSubmit) {
                $score -= 20;
            }

            if ($score < 0) {
                $score = 0;
            }

            $report->update([
                'status'         => 'completed',
                'total_score'    => $score,
                'is_late_submit' => $isLateSubmit,
            ]);

            ActivityLog::record(Auth::id(), 'SUBMIT_REPORT', "Menyelesaikan laporan shift ID: {$report->id} dengan skor $score");

            DB::commit();

            $msg = $isLateSubmit ? 'Laporan akhir shift berhasil disubmit, namun tercatat TERLAMBAT!' : 'Laporan akhir shift berhasil disubmit!';
            return redirect()->route('dashboard')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Submit Report Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses laporan Anda. Mohon coba beberapa saat lagi.');
        }
    }

    public function show(Report $report)
    {
        if ($report->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $report->load(['items.task', 'user']);

        $hotelId = $report->user->hotel_id;

        $prevReport = Report::where('hotel_id', $hotelId)
            ->where('id', '<', $report->id)
            ->orderBy('id', 'desc')
            ->first();

        $nextReport = Report::where('hotel_id', $hotelId)
            ->where('id', '>', $report->id)
            ->orderBy('id', 'asc')
            ->first();

        $totalReports = Report::where('hotel_id', $hotelId)->count();

        $currentIndex = Report::where('hotel_id', $hotelId)
            ->where('id', '<=', $report->id)
            ->count();

        return view('reports.show', compact('report', 'prevReport', 'nextReport', 'totalReports', 'currentIndex'));
    }

    public function history()
    {
        $user = Auth::user();

        $reports = Report::with('items.task')
            ->where('user_id', $user->id)
            ->orderBy('report_date', 'desc')
            ->paginate(15);

        return view('reports.history', compact('reports'));
    }

    public function destroy(Report $report)
    {
        if ($report->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        ActivityLog::record(Auth::id(), 'DELETE_REPORT', "Menghapus laporan shift ID: {$report->id}");

        $this->deleteReportImages($report);

        $report->delete();

        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Laporan Todo List berhasil dihapus.');
        }
        return redirect()->route('dashboard')->with('success', 'Laporan Todo List berhasil dihapus.');
    }
}
