<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Hotel;
use App\Models\User;
use App\Models\ReportItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function show(int $id): JsonResponse
    {
        $report = Report::with(['user', 'reportItems.task'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'html' => view('admin.reports.partials.detail-modal', compact('report'))->render()
        ]);
    }

    public function index(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->timezone('Asia/Jakarta')->subDays(6)->format('Y-m-d'));
        $endDate = $request->query('end_date', Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d'));
        $department = $request->query('department');
        $shiftId = $request->query('shift_id');
        $staffId = $request->query('staff_id');
        $hotelSlug = $request->query('hotel', 'wahyu');

        $currentHotel = Hotel::where('name', 'LIKE', '%' . $hotelSlug . '%')->first();
        $hotelId = $request->query('hotel_id', $currentHotel?->id);

        $hotels = Hotel::all();
        $staffList = User::where('role', 'staff')->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->orderBy('name')->get(['id', 'name']);

        $defaultDepartments = ['Front Office', 'Housekeeping', 'Engineering', 'Food & Beverage', 'Security', 'Human Resources', 'Accounting', 'Sales & Marketing'];
        $availableDepartments = collect(array_merge($defaultDepartments, User::whereNotNull('department')->pluck('department')->toArray()))->unique()->sort()->values();

        $baseQuery = Report::whereBetween('report_date', [$startDate, $endDate])
            ->when($hotelId, fn($q) => $q->whereHas('user', fn($u) => $u->where('hotel_id', $hotelId)))
            ->when($department, fn($q) => $q->whereHas('user', fn($u) => $u->where('department', $department)))
            ->when($shiftId, fn($q) => $q->where('shift_id', $shiftId))
            ->when($staffId, fn($q) => $q->where('user_id', $staffId));

        $totalKaryawanMasuk = (clone $baseQuery)->count();
        $laporanTerlambat = (clone $baseQuery)->where(fn($q) => $q->where('is_late', true)->orWhere('is_late_submit', true))->count();
        $laporanTepatWaktu = $totalKaryawanMasuk - $laporanTerlambat;

        $totalTugasTambahan = ReportItem::where('is_additional', true)->whereIn('report_id', (clone $baseQuery)->select('id'))->count();

        $chartData = (clone $baseQuery)
            ->selectRaw('report_date, SUM(CASE WHEN is_late = 1 OR is_late_submit = 1 THEN 1 ELSE 0 END) as telat, SUM(CASE WHEN is_late = 0 AND is_late_submit = 0 THEN 1 ELSE 0 END) as tepat')
            ->groupBy('report_date')->get()->keyBy('report_date');

        $chartDates = [];
        $chartTepat = [];
        $chartTelat = [];

        foreach (\Carbon\CarbonPeriod::create($startDate, $endDate) as $date) {
            $dateString = $date->format('Y-m-d');
            $chartDates[] = $date->format('d M');
            $chartTepat[] = $chartData->has($dateString) ? (int) $chartData[$dateString]->tepat : 0;
            $chartTelat[] = $chartData->has($dateString) ? (int) $chartData[$dateString]->telat : 0;
        }

        $reports = (clone $baseQuery)->with(['user.branch', 'items.task'])
            ->orderBy('report_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(50)->withQueryString();

        return view('admin.reports.summary', compact(
            'reports', 'hotels', 'startDate', 'endDate', 'hotelId', 'department', 'shiftId', 'staffId', 'staffList', 'hotelSlug', 'currentHotel',
            'totalKaryawanMasuk', 'laporanTepatWaktu', 'laporanTerlambat', 'totalTugasTambahan', 'chartDates', 'chartTepat', 'chartTelat', 'availableDepartments'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->timezone('Asia/Jakarta')->subDays(6)->format('Y-m-d'));
        $endDate = $request->query('end_date', Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d'));

        $hotelSlug = $request->query('hotel', 'wahyu');
        $currentHotel = Hotel::where('name', 'LIKE', '%' . $hotelSlug . '%')->first();

        $hotelId = $request->query('hotel_id', $currentHotel?->id);
        $department = $request->query('department');

        return Excel::download(new ReportsExport($startDate, $endDate, $hotelId, $department), "Rekap_Laporan_{$startDate}_sd_{$endDate}.xlsx");
    }
}
