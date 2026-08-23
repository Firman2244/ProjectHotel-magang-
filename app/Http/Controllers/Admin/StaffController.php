<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use App\Models\PointHistory;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Carbon\Carbon;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $reqHotel = $request->query('hotel');
        $currentHotel = $reqHotel ? Hotel::find($reqHotel) : Hotel::first();
        $hotelId = $currentHotel?->id;

        $staffs = User::where('role', '!=', 'admin')
            ->with('branch:id,name')
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
            ->get();

        $hotels = Hotel::all();
        $hotelSlug = $currentHotel?->id;
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
        $reqHotel = $request->query('hotel');
        $currentHotel = $reqHotel ? Hotel::find($reqHotel) : Hotel::first();
        $hotelId = $currentHotel?->id;

        $selectedMonth = $request->query('month', Carbon::now('Asia/Jakarta')->month);
        $selectedYear = $request->query('year', Carbon::now('Asia/Jakarta')->year);

        $usersQuery = User::with('branch:id,name')
            ->where('role', '!=', 'admin')
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
            ->addSelect([
                'total_score' => PointHistory::selectRaw('COALESCE(SUM(points), 0)')
                    ->whereColumn('user_id', 'users.id')
                    ->whereMonth('created_at', $selectedMonth)
                    ->whereYear('created_at', $selectedYear),
                'total_shift' => Report::selectRaw('COUNT(*)')
                    ->whereColumn('user_id', 'users.id')
                    ->where('status', 'completed')
                    ->whereMonth('report_date', $selectedMonth)
                    ->whereYear('report_date', $selectedYear)
            ]);

        $staffs = $usersQuery->get()->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'department' => $user->department,
                'role' => $user->role,
                'branch' => $user->branch ? $user->branch->name : '-',
                'total_shift' => $user->total_shift,
                'total_score' => (float) $user->total_score,
            ];
        })->filter(function($staff) {
            return $staff['total_score'] > 0 || $staff['total_shift'] > 0;
        })->sortByDesc('total_score')->values();

        $hotels = Hotel::all();
        $hotelSlug = $currentHotel?->id;
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $years = range(Carbon::now('Asia/Jakarta')->year - 2, Carbon::now('Asia/Jakarta')->year + 1);

        return view('admin.staff.scores', compact(
            'staffs', 'hotels', 'hotelSlug', 'hotelId',
            'selectedMonth', 'selectedYear', 'months', 'years'
        ));
    }

    public function pointHistoryModal(int $id)
    {
        $staff = User::with('branch:id,name')->findOrFail($id);
        $histories = PointHistory::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.staff.points-history', compact('staff', 'histories'));
    }
}
