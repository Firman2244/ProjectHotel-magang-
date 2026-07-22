<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $shiftId = $user->shift_id;
        $today = Carbon::today('Asia/Jakarta')->toDateString();

        if ($shiftId == 1) {
            $shiftName = 'Shift 1 (Pagi)';
            $todoDeadline = '07:00 - 07:30';
            $submitDeadlineTime = '15:30';
        } elseif ($shiftId == 2) {
            $shiftName = 'Shift 2 (Siang)';
            $todoDeadline = '13:00 - 13:30';
            $submitDeadlineTime = '21:30';
        } elseif ($shiftId == 3) {
            $shiftName = 'Shift 3 (Malam)';
            $todoDeadline = '22:00 - 22:30';
            $submitDeadlineTime = '06:30';
        } else {
            $shiftName = 'Shift Tidak Valid';
            $todoDeadline = '--:--';
            $submitDeadlineTime = '--:--';
        }

        $todayReportCompleted = Report::where('user_id', $user->id)
            ->where('report_date', $today)
            ->where('status', 'completed')
            ->exists();

        $todayReportPlanned = Report::where('user_id', $user->id)
            ->where('report_date', $today)
            ->where('status', 'planned')
            ->first();

        $query = Report::where('user_id', $user->id);

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $reports = $query->orderBy('created_at', 'desc')->get();
        $totalReports = Report::where('user_id', $user->id)->count();

        $years = Report::where('user_id', $user->id)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('dashboard', compact(
            'user', 'shiftName', 'todoDeadline', 'submitDeadlineTime',
            'reports', 'totalReports', 'years', 'todayReportCompleted', 'todayReportPlanned'
        ));
    }
}
