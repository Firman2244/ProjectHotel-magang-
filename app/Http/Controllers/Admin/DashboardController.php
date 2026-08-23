<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use App\Models\Hotel;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();

        $reqHotel = $request->query('hotel');
        $currentHotel = $reqHotel ? Hotel::find($reqHotel) : Hotel::first();
        $hotelId = $currentHotel ? $currentHotel->id : null;

        $totalStaff = User::where('role', 'staff')
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
            ->count();

        $baseTodayQuery = Report::where('report_date', $today)
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId));

        $submittedCount = (clone $baseTodayQuery)->where('status', 'completed')->count();
        $lateCount = (clone $baseTodayQuery)->where('is_late', true)->count();

        $reports = Report::with(['user:id,name,department,hotel_id', 'items.task:id,name'])
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
            ->when($request->filled('date'), fn($q) => $q->whereDate('report_date', $request->date), fn($q) => $q->whereDate('report_date', $today))
            ->when($request->filled('department'), fn($q) => $q->whereHas('user', fn($u) => $u->where('department', $request->department)))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $departments = User::where('role', 'staff')
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->pluck('department');

        return view('admin.dashboard', compact('totalStaff', 'submittedCount', 'lateCount', 'reports', 'departments', 'currentHotel'));
    }
}
