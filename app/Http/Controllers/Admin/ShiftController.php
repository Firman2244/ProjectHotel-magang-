<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $hotelSlug = $request->query('hotel', 'wahyu');
        $currentHotel = Hotel::where('name', 'LIKE', '%' . $hotelSlug . '%')->first();

        $department = $request->query('department');

        // Query dasar khusus staff dan filter berdasarkan hotel aktif
        $query = User::where('role', 'staff');

        if ($currentHotel) {
            $query->where('hotel_id', $currentHotel->id);
        }

        $departments = (clone $query)->select('department')->distinct()->pluck('department');

        if ($department) {
            $query->where('department', $department);
        }

        $staffUnassigned = (clone $query)->whereNull('shift_id')->get();
        $staffShift1 = (clone $query)->where('shift_id', 1)->get();
        $staffShift2 = (clone $query)->where('shift_id', 2)->get();
        $staffShift3 = (clone $query)->where('shift_id', 3)->get();

        $shifts = Shift::all();

        return view('admin.shifts.index', compact('staffUnassigned', 'staffShift1', 'staffShift2', 'staffShift3', 'shifts', 'departments', 'department', 'hotelSlug'));
    }

    public function updateShift(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'nullable|exists:shifts,id',
        ]);

        $user = User::find($request->user_id);
        $user->shift_id = $request->shift_id;
        $user->save();

        return response()->json(['success' => true]);
    }
}
