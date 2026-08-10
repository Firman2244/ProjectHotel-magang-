<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'admin') return redirect()->route('admin.dashboard');

        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $shiftConfig = match ($user->shift_id) {
            1 => ['name' => 'Shift 1 (Pagi)', 'todo' => '07:00 - 07:30', 'submit' => '15:30'],
            2 => ['name' => 'Shift 2 (Siang)', 'todo' => '13:00 - 13:30', 'submit' => '21:30'],
            3 => ['name' => 'Shift 3 (Malam)', 'todo' => '22:00 - 22:30', 'submit' => '06:30'],
            default => ['name' => 'Shift Tidak Valid', 'todo' => '--:--', 'submit' => '--:--'],
        };

        $baseReportQuery = Report::where('user_id', $user->id)->where('report_date', $today);
        $todayReport = (clone $baseReportQuery)->first();

        $todayReportCompleted = $todayReport && $todayReport->status === 'completed';
        $todayReportPlanned = $todayReport && $todayReport->status === 'planned' ? $todayReport : null;

        $reports = Report::where('user_id', $user->id)
            ->when($request->filled('year'), fn($q) => $q->whereYear('created_at', $request->year))
            ->when($request->filled('month'), fn($q) => $q->whereMonth('created_at', $request->month))
            ->when($request->filled('date'), fn($q) => $q->whereDate('created_at', $request->date))
            ->orderBy('created_at', 'desc')->get();

        $totalReports = Report::where('user_id', $user->id)->count();
        $years = Report::where('user_id', $user->id)->selectRaw('YEAR(created_at) as year')->distinct()->orderBy('year', 'desc')->pluck('year');

        $currentMonth = Carbon::now('Asia/Jakarta')->month;
        $currentYear = Carbon::now('Asia/Jakarta')->year;

        $lbMonth = $request->input('lb_month', $currentMonth);
        $lbDept = $request->input('lb_dept', '');

        $lbQuery = Report::where('hotel_id', $user->hotel_id)
            ->where('status', 'completed')
            ->whereMonth('report_date', $lbMonth)
            ->whereYear('report_date', $currentYear);

        if (!empty($lbDept)) {
            $lbQuery->whereHas('user', function($q) use ($lbDept) {
                $q->where('department', $lbDept);
            });
        }

        $allHotelScores = $lbQuery->selectRaw('user_id, SUM(total_score) as total_points')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->with('user')
            ->get();

        $leaderboard = $allHotelScores->take(5);

        $myRankIndex = $allHotelScores->search(fn($item) => $item->user_id == $user->id);
        $myRank = $myRankIndex !== false ? $myRankIndex + 1 : '-';
        $myTotalPoints = $allHotelScores->firstWhere('user_id', $user->id)->total_points ?? 0;

        $lbDepartments = User::where('hotel_id', $user->hotel_id)->whereNotNull('department')->select('department')->distinct()->pluck('department');

        return view('dashboard', array_merge(
            ['user' => $user, 'reports' => $reports, 'totalReports' => $totalReports, 'years' => $years, 'todayReportCompleted' => $todayReportCompleted, 'todayReportPlanned' => $todayReportPlanned, 'todayReport' => $todayReport, 'leaderboard' => $leaderboard, 'lbDepartments' => $lbDepartments, 'lbMonth' => $lbMonth, 'lbDept' => $lbDept, 'myRank' => $myRank, 'myTotalPoints' => $myTotalPoints],
            ['shiftName' => $shiftConfig['name'], 'todoDeadline' => $shiftConfig['todo'], 'submitDeadlineTime' => $shiftConfig['submit']]
        ));
    }
}
