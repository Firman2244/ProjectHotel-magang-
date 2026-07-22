<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();

        // Ambil hotel aktif dari request (default: wahyu)
        $currentHotel = $request->get('hotel', 'wahyu');

        // Filter staf berdasarkan hotel yang dipilih
        $totalStaff = User::where('role', 'staff')
            ->where('hotel', $currentHotel)
            ->count();

        $todayReports = Report::where('report_date', $today)
            ->whereHas('user', function($q) use ($currentHotel) {
                $q->where('hotel', $currentHotel);
            })
            ->get();

        $submittedCount = $todayReports->where('status', 'completed')->count();
        $lateCount = $todayReports->where('is_late', true)->count();

        $query = Report::with(['user', 'items.task'])
            ->whereHas('user', function($q) use ($currentHotel) {
                $q->where('hotel', $currentHotel);
            });

        if ($request->filled('date')) {
            $query->whereDate('report_date', $request->date);
        } else {
            $query->whereDate('report_date', $today);
        }

        if ($request->filled('department')) {
            $dept = $request->department;
            $query->whereHas('user', function($q) use ($dept) {
                $q->where('department', $dept);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        $departments = User::where('role', 'staff')
            ->where('hotel', $currentHotel)
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->pluck('department');

        return view('admin.dashboard', compact(
            'totalStaff', 'submittedCount', 'lateCount', 'reports', 'departments', 'currentHotel'
        ));
    }
}
