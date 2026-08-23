<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Shift;
use App\Models\Note;
use App\Models\PointHistory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        assert($user instanceof User);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();

        $shiftId = $user->shift_id;
        $activeReportDate = ($shiftId == 3 && $now->hour < 12)
            ? $now->copy()->subDay()->toDateString()
            : $today;

        $totalReports = Report::where('user_id', $user->id)->count();

        $shift = Shift::find($shiftId);
        $shiftName = $shift ? $shift->name : 'Belum Diatur';
        $todoDeadline = $shift
            ? Carbon::parse($shift->start_time)->format('H:i') . ' - ' . Carbon::parse($shift->deadline_time)->format('H:i')
            : '--:--';
        $submitDeadlineTime = $shift ? Carbon::parse($shift->deadline_time)->format('H:i') : '15:30';

        $todayReport = Report::where('user_id', $user->id)
            ->where('report_date', $activeReportDate)
            ->first();

        $todayReportCompleted = $todayReport && $todayReport->status === 'completed';

        $todayReportPlanned = Report::where('user_id', $user->id)
            ->where('report_date', $activeReportDate)
            ->where('status', '!=', 'completed')
            ->latest()
            ->first();

        $hasDoubleShiftPermit = $user->hasActiveDoubleShiftPermit($now);

        $baseReportCompleted = Report::where('user_id', $user->id)
            ->where('report_date', $activeReportDate)
            ->where('shift_id', $shiftId)
            ->where('status', 'completed')
            ->exists();

        // [FIX LAG] Tambah 'user' ke eager loading untuk mematikan query N+1
        $reportsQuery = Report::with(['items.task', 'user'])->where('user_id', $user->id);

        if ($request->filled('date')) {
            $reportsQuery->whereDate('created_at', $request->date);
        }
        if ($request->filled('month')) {
            $reportsQuery->whereMonth('created_at', $request->month);
        }
        if ($request->filled('year')) {
            $reportsQuery->whereYear('created_at', $request->year);
        }

        $reports = $reportsQuery->orderBy('created_at', 'desc')->limit(50)->get();

        $years = Report::where('user_id', $user->id)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year');
        if ($years->isEmpty()) {
            $years = collect([$now->year]);
        }

        $lbMonth = $request->query('lb_month', $now->month);
        $lbYear = $request->query('lb_year', $now->year);
        $lbDept = $request->query('lb_dept');

        $lbDepartments = User::where('role', 'staff')
            ->where('hotel_id', $user->hotel_id)
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        $leaderboard = PointHistory::select('user_id')
            ->selectRaw('SUM(points) as total_points')
            ->whereHas('user', fn ($query) => $query->where('hotel_id', $user->hotel_id))
            ->whereMonth('created_at', $lbMonth)
            ->whereYear('created_at', $lbYear)
            ->when($lbDept, function ($q) use ($lbDept) {
                $q->whereHas('user', fn ($query) => $query->where('department', $lbDept));
            })
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->with(['user' => fn($query) => $query->select('id', 'name', 'department')])
            ->get();

        $myTotalPoints = PointHistory::where('user_id', $user->id)->sum('points');
        $pointHistories = PointHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $rankIndex = $leaderboard->search(fn ($lead) => $lead->user_id == $user->id);
        $myRank = $rankIndex !== false ? $rankIndex + 1 : null;

        $engineeringTasks = collect();
        if ($user->department === 'Engineering') {
            $engineeringTasks = Note::with(['user:id,name', 'resolver:id,name'])
                ->where('category', 'Kerusakan')
                ->whereIn('status', ['open', 'resolved'])
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
        }

        $engineeringStaff = User::where('department', 'Engineering')
            ->where('hotel_id', $user->hotel_id)
            ->where('id', '!=', $user->id)
            ->select('id', 'name')
            ->get();

        return view('dashboard', compact(
            'user', 'totalReports', 'shiftName', 'todoDeadline', 'submitDeadlineTime',
            'todayReportCompleted', 'todayReportPlanned', 'reports', 'years',
            'hasDoubleShiftPermit', 'baseReportCompleted', 'todayReport',
            'leaderboard', 'myTotalPoints', 'myRank', 'lbDepartments', 'lbMonth', 'lbDept',
            'engineeringTasks', 'engineeringStaff', 'pointHistories'
        ));
    }
}
