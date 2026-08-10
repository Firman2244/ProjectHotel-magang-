<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Carbon\Carbon;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $hotelSlug = $request->query('hotel', 'wahyu');
        $currentHotel = Hotel::where('name', 'LIKE', "%{$hotelSlug}%")->first();

        $staffs = User::where('role', '!=', 'admin')
            ->with('branch')
            ->when($currentHotel, fn($q) => $q->where('hotel_id', $currentHotel->id))
            ->get();

        $hotels = Hotel::all();
        return view('admin.staff.index', compact('staffs', 'hotels', 'hotelSlug', 'currentHotel'));
    }

    public function create()
    {
        return view('admin.staff.create', ['hotels' => Hotel::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'hotel_id' => ['required', 'exists:hotels,id'],
            'department' => ['required', 'string'],
        ]);

        User::create(array_merge($request->only('name', 'email', 'hotel_id', 'department'), [
            'password' => Hash::make($request->password),
            'role' => 'staff'
        ]));

        return redirect()->route('admin.staff.index')->with('success', 'Akun staf berhasil didaftarkan!');
    }

    public function edit(User $staff)
    {
        return view('admin.staff.edit', ['staff' => $staff, 'hotels' => Hotel::all()]);
    }

    public function update(Request $request, User $staff)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$staff->id],
            'hotel_id' => ['required', 'exists:hotels,id'],
            'department' => ['required', 'string'],
        ]);

        $data = $request->only('name', 'email', 'hotel_id', 'department');
        if ($request->filled('password')) {
            $request->validate(['password' => ['confirmed', Rules\Password::defaults()]]);
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);
        return redirect()->route('admin.staff.index')->with('success', 'Data staf berhasil diperbarui!');
    }

    public function destroy(User $staff)
    {
        $staff->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Akun staf berhasil dihapus!');
    }

    public function leaderboard(Request $request)
    {
        $hotelSlug = $request->query('hotel', 'wahyu');
        $hotelId = Hotel::where('name', 'LIKE', "%{$hotelSlug}%")->value('id');
        $hotels = Hotel::all();

        $selectedMonth = $request->query('month', Carbon::now('Asia/Jakarta')->month);
        $selectedYear = $request->query('year', Carbon::now('Asia/Jakarta')->year);
        $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $years = range(Carbon::now('Asia/Jakarta')->year - 2, Carbon::now('Asia/Jakarta')->year + 1);

        $staffs = User::where('role', 'staff')
            ->with('branch')
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
            ->withCount(['reports as total_shift' => fn($q) => $q->where('status', 'completed')->whereMonth('report_date', $selectedMonth)->whereYear('report_date', $selectedYear)])
            ->withSum(['reports as total_score' => fn($q) => $q->where('status', 'completed')->whereMonth('report_date', $selectedMonth)->whereYear('report_date', $selectedYear)], 'total_score')
            ->having('total_shift', '>', 0)
            ->get()
            ->map(fn($staff) => [
                'id' => $staff->id, 'name' => $staff->name, 'email' => $staff->email, 'department' => $staff->department,
                'branch' => $staff->branch?->name ?? '-', 'total_shift' => $staff->total_shift ?? 0, 'total_score' => $staff->total_score ?? 0,
            ])->sortByDesc('total_score')->values();

        return view('admin.staff.scores', compact('staffs', 'hotels', 'hotelSlug', 'hotelId', 'selectedMonth', 'selectedYear', 'months', 'years'));
    }
}
