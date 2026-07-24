<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Report;
use App\Models\ReportItem;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        $tasks = Task::where('department', $user->department)->get();

        return view('reports.create', compact('user', 'tasks'));
    }

    private function compressAndStoreImage($file)
    {
        if (!$file) return null;

        if (!extension_loaded('gd') ||
            !function_exists('imagecreatefromjpeg') ||
            !function_exists('imagecreatefrompng') ||
            !function_exists('imagecreatefromwebp')) {
            return $file->store('reports', 'public');
        }

        $filename = 'reports/' . uniqid() . '.jpg';
        $destinationPath = storage_path('app/public/' . $filename);

        list($width, $height, $type) = \getimagesize($file->getRealPath());

        $maxW = 1200;
        if ($width > $maxW) {
            $newW = $maxW;
            $newH = round($height * ($maxW / $width));
        } else {
            $newW = $width;
            $newH = $height;
        }

        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImg = \imagecreatefromjpeg($file->getRealPath());
                break;
            case IMAGETYPE_PNG:
                $srcImg = \imagecreatefrompng($file->getRealPath());
                break;
            case IMAGETYPE_WEBP:
                $srcImg = \imagecreatefromwebp($file->getRealPath());
                break;
            default:
                return $file->store('reports', 'public');
        }

        $dstImg = \imagecreatetruecolor($newW, $newH);

        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
            $white = \imagecolorallocate($dstImg, 255, 255, 255);
            \imagefill($dstImg, 0, 0, $white);
        }

        \imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $width, $height);
        \imagejpeg($dstImg, $destinationPath, 80);

        \imagedestroy($srcImg);
        \imagedestroy($dstImg);

        return $filename;
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
            }
            $isLate = $now->greaterThan($deadline);
        }

        $report = Report::create([
            'user_id'     => $user->id,
            'hotel_id'    => $user->hotel_id,
            'shift_id'    => $user->shift_id,
            'report_date' => $now->toDateString(),
            'status'      => 'planned',
            'is_late'     => $isLate,
        ]);

        if ($request->has('items')) {
            foreach ($request->items as $item) {
                ReportItem::create([
                    'report_id'     => $report->id,
                    'task_id'       => $item['task_id'],
                    'is_additional' => 0,
                ]);
            }
        }

        $msg = $isLate ? 'Todo list berhasil dikirim, namun tercatat TERLAMBAT!' : 'Todo list berhasil dikirim ke admin!';
        return redirect()->route('dashboard')->with('success', $msg);
    }

    public function updateFinal(Request $request, Report $report)
    {
        $request->validate([
            'items.*.before_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'items.*.after_image'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'items.*.notes'        => 'nullable|string|max:255',
            'new_items.*.task_name'=> 'nullable|string|max:255',
            'new_items.*.before_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'new_items.*.after_image'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'new_items.*.notes'        => 'nullable|string|max:255',
        ]);

        if ($request->has('items')) {
            foreach ($request->items as $itemId => $itemData) {
                $item = ReportItem::find($itemId);
                if ($item && $item->report_id == $report->id) {

                    $beforePath = isset($itemData['before_image']) ? $this->compressAndStoreImage($itemData['before_image']) : $item->before_image;
                    $afterPath = isset($itemData['after_image']) ? $this->compressAndStoreImage($itemData['after_image']) : $item->after_image;

                    $item->update([
                        'before_image' => $beforePath,
                        'after_image'  => $afterPath,
                        'notes'        => $itemData['notes'] ?? $item->notes,
                    ]);
                }
            }
        }

        if ($request->has('new_items')) {
            foreach ($request->new_items as $newItem) {
                if (!empty($newItem['task_name'])) {
                    $beforePath = isset($newItem['before_image']) ? $this->compressAndStoreImage($newItem['before_image']) : null;
                    $afterPath = isset($newItem['after_image']) ? $this->compressAndStoreImage($newItem['after_image']) : null;

                    ReportItem::create([
                        'report_id'     => $report->id,
                        'task_id'       => null,
                        'before_image'  => $beforePath,
                        'after_image'   => $afterPath,
                        'notes'         => "Tugas Tambahan: " . $newItem['task_name'] . (!empty($newItem['notes']) ? " - " . $newItem['notes'] : ""),
                        'is_additional' => 1,
                    ]);
                }
            }
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

        $report->update([
            'status'         => 'completed',
            'is_late_submit' => $isLateSubmit,
        ]);

        $msg = $isLateSubmit ? 'Laporan akhir shift berhasil disubmit, namun tercatat TERLAMBAT!' : 'Laporan akhir shift berhasil disubmit!';
        return redirect()->route('dashboard')->with('success', $msg);
    }

    public function show(Report $report)
    {
        if ($report->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $report->load(['items.task', 'user']);

        $hotelId = $report->user->hotel_id;
        $reportDate = $report->report_date;

        $prevReport = Report::where('report_date', $reportDate)
            ->whereHas('user', function($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            })
            ->where('id', '<', $report->id)
            ->orderBy('id', 'desc')
            ->first();

        $nextReport = Report::where('report_date', $reportDate)
            ->whereHas('user', function($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            })
            ->where('id', '>', $report->id)
            ->orderBy('id', 'asc')
            ->first();

        $totalReports = Report::where('report_date', $reportDate)
            ->whereHas('user', function($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            })->count();

        $currentIndex = Report::where('report_date', $reportDate)
            ->whereHas('user', function($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            })->where('id', '<=', $report->id)->count();

        return view('reports.show', compact('report', 'prevReport', 'nextReport', 'totalReports', 'currentIndex'));
    }

    public function destroy(Report $report)
    {
        if ($report->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $report->delete();

        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Laporan Todo List berhasil dihapus.');
        }
        return redirect()->route('dashboard')->with('success', 'Laporan Todo List berhasil dihapus.');
    }
}
