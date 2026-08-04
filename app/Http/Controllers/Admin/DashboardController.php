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

        // Ambil hotel aktif dari request (default: wahyu)
        $hotelSlug = $request->get('hotel', 'wahyu');

        // FIX INKONSISTENSI:
        // Sebelumnya controller ini filter pakai kolom string `users.hotel`,
        // sedangkan controller Admin lain (StaffController, ShiftController)
        // filter pakai `users.hotel_id` (foreign key). Dua sumber data yang
        // seharusnya sama tapi bisa saja tidak sinkron kalau salah satu update
        // tidak konsisten. Sekarang SEMUA filter hotel pakai hotel_id saja,
        // resolusi slug->id dilakukan sekali di sini.
        $resolvedHotel = Hotel::where('name', 'LIKE', '%' . $hotelSlug . '%')->first();
        $hotelId = $resolvedHotel?->id;

        $totalStaff = User::where('role', 'staff')
            ->when($hotelId, fn ($q) => $q->where('hotel_id', $hotelId))
            ->count();

        // OPTIMASI: hitung submittedCount & lateCount langsung lewat query
        // agregat (COUNT), bukan tarik semua baris report hari ini ke memory
        // lalu di-filter pakai Collection (->where()->count()). Untuk hari
        // dengan banyak laporan, ini jauh lebih ringan karena MySQL yang
        // menghitung, bukan PHP.
        $baseTodayQuery = Report::where('report_date', $today)
            ->when($hotelId, fn ($q) => $q->where('hotel_id', $hotelId));

        $submittedCount = (clone $baseTodayQuery)->where('status', 'completed')->count();
        $lateCount = (clone $baseTodayQuery)->where('is_late', true)->count();

        $query = Report::with(['user', 'items.task'])
            ->when($hotelId, fn ($q) => $q->where('hotel_id', $hotelId));

        if ($request->filled('date')) {
            $query->whereDate('report_date', $request->date);
        } else {
            $query->whereDate('report_date', $today);
        }

        if ($request->filled('department')) {
            $dept = $request->department;
            $query->whereHas('user', function ($q) use ($dept) {
                $q->where('department', $dept);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // OPTIMASI: paginate(20) menggantikan get(). Halaman ini menampilkan
        // laporan + modal detail (foto) untuk setiap baris, jadi ini kombinasi
        // paling penting untuk mencegah render ratusan gambar sekaligus.
        // withQueryString() supaya filter (date/department/status) tetap
        // terbawa saat pindah halaman pagination.
        $reports = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $departments = User::where('role', 'staff')
            ->when($hotelId, fn ($q) => $q->where('hotel_id', $hotelId))
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->pluck('department');

        // currentHotel dikirim sebagai string slug (sama seperti versi asli)
        // supaya tidak perlu ubah apapun di view admin.dashboard.blade.php
        $currentHotel = $hotelSlug;

        return view('admin.dashboard', compact(
            'totalStaff', 'submittedCount', 'lateCount', 'reports', 'departments', 'currentHotel'
        ));
    }
}
