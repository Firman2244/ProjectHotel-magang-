<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\ReportItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkloadController extends Controller
{
    /**
     * Menampilkan daftar pekerjaan berdasarkan kategori beban kerja.
     * Data tidak di-load otomatis untuk mencegah N+1 & server down.
     * Menggunakan fitur dinamis Pagination & Redirect.
     */
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->timezone('Asia/Jakarta')->subDays(6)->format('Y-m-d'));
        $endDate = $request->query('end_date', Carbon::now()->timezone('Asia/Jakarta')->format('Y-m-d'));
        $department = $request->query('department');

        // Parameter baru sesuai request owner
        $difficulty = $request->query('difficulty'); // ringan, sedang, berat, semua
        $perPage = $request->query('per_page', 12); // default 12 item per page
        $filterApplied = $request->has('filter_applied'); // Cek apakah tombol filter sudah ditekan

        $reqHotel = $request->query('hotel');
        $currentHotel = $reqHotel ? Hotel::find($reqHotel) : Hotel::first();
        $hotelId = $currentHotel ? $currentHotel->id : null;

        $hotels = Hotel::all();
        $defaultDepartments = ['Front Office', 'Housekeeping', 'Engineering', 'Food & Beverage', 'Security', 'Human Resources', 'Accounting', 'Sales & Marketing'];
        $availableDepartments = collect($defaultDepartments)->unique()->sort()->values();

        $items = null;

        // Hanya jalankan query database JIKA admin sudah memencet tombol Filter & memilih kategori
        if ($filterApplied && !empty($difficulty)) {
            $query = ReportItem::whereNotNull('difficulty')
                ->with([
                    'task:id,name',
                    'report:id,user_id,hotel_id,report_date',
                    'report.user:id,name,department',
                ])
                ->whereHas('report', function ($q) use ($startDate, $endDate, $hotelId, $department) {
                    $q->whereBetween('report_date', [$startDate, $endDate]);
                    $q->when($hotelId, fn($q2) => $q2->where('hotel_id', $hotelId));
                    $q->when($department, fn($q2) => $q2->whereHas('user', fn($u) => $u->where('department', $department)));
                });

            // Filter spesifik kategori jika tidak memilih "semua"
            if ($difficulty !== 'semua') {
                $query->where('difficulty', $difficulty);
            }

            // Gunakan pagination, bukan get()
            $items = $query->orderByDesc('updated_at')->paginate((int) $perPage)->withQueryString();
        }

        return view('admin.workload.index', compact(
            'items', 'hotels', 'currentHotel', 'hotelId', 'startDate', 'endDate',
            'department', 'availableDepartments', 'difficulty', 'perPage', 'filterApplied'
        ));
    }
}
