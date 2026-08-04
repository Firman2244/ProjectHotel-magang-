<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Hotel;
use App\Models\User;
use App\Models\ReportItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportSummaryController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->timezone('Asia/Jakarta')->subDays(6)->format('Y-m-d'));
        $endDate = $request->query('end_date', Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d'));
        $department = $request->query('department');

        $hotelSlug = $request->query('hotel', 'wahyu');
        $currentHotel = Hotel::where('name', 'LIKE', '%' . $hotelSlug . '%')->first();

        $hotelId = $request->query('hotel_id', $currentHotel ? $currentHotel->id : null);

        $hotels = Hotel::all();

        $defaultDepartments = [
            'Front Office',
            'Housekeeping',
            'Engineering',
            'Food & Beverage',
            'Security',
            'Human Resources',
            'Accounting',
            'Sales & Marketing'
        ];

        $dbDepartments = User::whereNotNull('department')
            ->pluck('department')
            ->toArray();

        $availableDepartments = collect(array_merge($defaultDepartments, $dbDepartments))
            ->unique()
            ->sort()
            ->values();

        $baseQuery = Report::whereBetween('report_date', [$startDate, $endDate]);

        if ($hotelId) {
            $baseQuery->whereHas('user', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }

        if ($department) {
            $baseQuery->whereHas('user', function ($q) use ($department) {
                $q->where('department', $department);
            });
        }

        $totalKaryawanMasuk = (clone $baseQuery)->count();

        $laporanTerlambat = (clone $baseQuery)->where(function ($q) {
            $q->where('is_late', true)->orWhere('is_late_submit', true);
        })->count();

        $laporanTepatWaktu = $totalKaryawanMasuk - $laporanTerlambat;

        $totalTugasTambahan = ReportItem::where('is_additional', true)
            ->whereIn('report_id', (clone $baseQuery)->select('id'))
            ->count();

        $chartDates = [];
        $chartTepat = [];
        $chartTelat = [];

        $chartData = (clone $baseQuery)
            ->selectRaw('report_date, SUM(CASE WHEN is_late = 1 OR is_late_submit = 1 THEN 1 ELSE 0 END) as telat, SUM(CASE WHEN is_late = 0 AND is_late_submit = 0 THEN 1 ELSE 0 END) as tepat')
            ->groupBy('report_date')
            ->get()
            ->keyBy('report_date');

        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $chartDates[] = $date->format('d M');

            if ($chartData->has($dateString)) {
                $chartTepat[] = (int) $chartData[$dateString]->tepat;
                $chartTelat[] = (int) $chartData[$dateString]->telat;
            } else {
                $chartTepat[] = 0;
                $chartTelat[] = 0;
            }
        }

        $reports = (clone $baseQuery)->with(['user.branch', 'items.task'])
            ->orderBy('report_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(50)
            ->withQueryString();

        return view('admin.reports.summary', compact(
            'reports', 'hotels', 'startDate', 'endDate', 'hotelId', 'department', 'hotelSlug', 'currentHotel',
            'totalKaryawanMasuk', 'laporanTepatWaktu', 'laporanTerlambat', 'totalTugasTambahan',
            'chartDates', 'chartTepat', 'chartTelat', 'availableDepartments'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->timezone('Asia/Jakarta')->subDays(6)->format('Y-m-d'));
        $endDate = $request->query('end_date', Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d'));

        $hotelSlug = $request->query('hotel', 'wahyu');
        $currentHotel = Hotel::where('name', 'LIKE', '%' . $hotelSlug . '%')->first();

        $hotelId = $request->query('hotel_id', $currentHotel ? $currentHotel->id : null);
        $department = $request->query('department');

        $fileName = 'Rekap_Laporan_' . $startDate . '_sd_' . $endDate . '.xlsx';

        return Excel::download(new ReportsExport($startDate, $endDate, $hotelId, $department), $fileName);
    }
}
