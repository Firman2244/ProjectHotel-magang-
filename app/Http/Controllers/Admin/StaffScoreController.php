<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StaffScoreController extends Controller
{
    public function index(Request $request)
    {
        $hotelSlug = $request->query('hotel', 'wahyu');
        $currentHotel = Hotel::where('name', 'LIKE', '%' . $hotelSlug . '%')->first();
        $hotelId = $request->query('hotel_id', $currentHotel ? $currentHotel->id : null);

        $hotels = Hotel::all();

        $selectedMonth = $request->query('month', Carbon::now('Asia/Jakarta')->month);
        $selectedYear = $request->query('year', Carbon::now('Asia/Jakarta')->year);

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $currentYear = Carbon::now('Asia/Jakarta')->year;
        $years = range($currentYear - 2, $currentYear + 1);

        $staffQuery = User::where('role', 'staff')
            ->with('branch')
            ->withCount(['reports as total_shift' => function($q) use ($selectedMonth, $selectedYear) {
                $q->where('status', 'completed')
                  ->whereMonth('report_date', $selectedMonth)
                  ->whereYear('report_date', $selectedYear);
            }])
            ->withSum(['reports as total_score' => function($q) use ($selectedMonth, $selectedYear) {
                $q->where('status', 'completed')
                  ->whereMonth('report_date', $selectedMonth)
                  ->whereYear('report_date', $selectedYear);
            }], 'total_score')
            ->having('total_shift', '>', 0);

        if ($hotelId) {
            $staffQuery->where('hotel_id', $hotelId);
        }

        $staffs = $staffQuery->get()->map(function($staff) {
            return [
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'department' => $staff->department,
                'branch' => $staff->branch ? $staff->branch->name : '-',
                'total_shift' => $staff->total_shift ?? 0,
                'total_score' => $staff->total_score ?? 0,
            ];
        })->sortByDesc('total_score')->values();

        return view('admin.staff.scores', compact(
            'staffs', 'hotels', 'hotelSlug', 'hotelId',
            'selectedMonth', 'selectedYear', 'months', 'years'
        ));
    }
}
