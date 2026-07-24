<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        // Tangkap parameter hotel dari sidebar (default: 'wahyu')
        $hotelSlug = $request->query('hotel', 'wahyu');

        // Cari data hotel berdasarkan slug/nama untuk mendapatkan ID-nya
        $currentHotel = Hotel::where('name', 'LIKE', '%' . $hotelSlug . '%')->first();

        // Query staf: filter berdasarkan hotel_id jika hotel ditemukan
        $query = User::where('role', '!=', 'admin')->with('branch');

        if ($currentHotel) {
            $query->where('hotel_id', $currentHotel->id);
        }

        $staffs = $query->get();
        $hotels = Hotel::all();

        return view('admin.staff.index', compact('staffs', 'hotels', 'hotelSlug', 'currentHotel'));
    }

    public function create()
    {
        $hotels = Hotel::all();
        return view('admin.staff.create', compact('hotels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'hotel_id' => ['required', 'exists:hotels,id'],
            'department' => ['required', 'string'],
            'shift_id' => ['required', 'in:1,2,3'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'hotel_id' => $request->hotel_id,
            'department' => $request->department,
            'shift_id' => $request->shift_id,
            'role' => 'staff',
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Akun staf berhasil didaftarkan!');
    }

    public function edit(User $staff)
    {
        $hotels = Hotel::all();
        return view('admin.staff.edit', compact('staff', 'hotels'));
    }

    public function update(Request $request, User $staff)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$staff->id],
            'hotel_id' => ['required', 'exists:hotels,id'],
            'department' => ['required', 'string'],
            'shift_id' => ['required', 'in:1,2,3'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'hotel_id' => $request->hotel_id,
            'department' => $request->department,
            'shift_id' => $request->shift_id,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
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
}
