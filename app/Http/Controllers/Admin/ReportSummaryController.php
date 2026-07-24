<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportSummaryController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tangkap parameter filter waktu & departemen
        $startDate = $request->query('start_date', Carbon::now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->query('end_date', Carbon::now()->format('Y-m-d'));
        $department = $request->query('department');

        // 2. Tangkap parameter hotel aktif dari sidebar (slug/nama seperti 'wahyu' atau 'nirwana')
        $hotelSlug = $request->query('hotel', 'wahyu');
        $currentHotel = Hotel::where('name', 'LIKE', '%' . $hotelSlug . '%')->first();

        // Bisa juga menangkap manual dari dropdown filter view jika ada
        $hotelId = $request->query('hotel_id', $currentHotel ? $currentHotel->id : null);

        $hotels = Hotel::all();

        // 3. Query Utama Laporan
        $query = Report::with(['user.branch', 'items'])
            ->whereBetween('report_date', [$startDate, $endDate]);

        // Filter berdasarkan hotel aktif/pilihan
        if ($hotelId) {
            $query->whereHas('user', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }

        // Filter berdasarkan departemen
        if ($department) {
            $query->whereHas('user', function ($q) use ($department) {
                $q->where('department', $department);
            });
        }

        $reports = $query->orderBy('report_date', 'desc')->orderBy('created_at', 'desc')->get();

        // 4. Kalkulasi Matriks Kinerja
        $totalKaryawanMasuk = $reports->count();
        $laporanTerlambat = $reports->filter(function ($r) {
            return $r->is_late || $r->is_late_submit;
        })->count();
        $laporanTepatWaktu = $totalKaryawanMasuk - $laporanTerlambat;

        // Hitung Anomali Beban Kerja (Total Tugas Tambahan)
        $totalTugasTambahan = 0;
        foreach ($reports as $r) {
            $totalTugasTambahan += $r->items->where('is_additional', true)->count();
        }

        // 5. Siapkan Data untuk Grafik (Chart.js)
        $chartDates = [];
        $chartTepat = [];
        $chartTelat = [];

        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $chartDates[] = $date->format('d M');

            $dailyReports = $reports->where('report_date', $dateString);
            $dailyTelat = $dailyReports->filter(fn($r) => $r->is_late || $r->is_late_submit)->count();
            $dailyTepat = $dailyReports->count() - $dailyTelat;

            $chartTepat[] = $dailyTepat;
            $chartTelat[] = $dailyTelat;
        }

        return view('admin.reports.summary', compact(
            'reports', 'hotels', 'startDate', 'endDate', 'hotelId', 'department', 'hotelSlug', 'currentHotel',
            'totalKaryawanMasuk', 'laporanTepatWaktu', 'laporanTerlambat', 'totalTugasTambahan',
            'chartDates', 'chartTepat', 'chartTelat'
        ));
    }
}
