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
        $currentHotel = Hotel::where('name', 'LIKE', "%{$hotelSlug}%")->first();
        $department = $request->query('department');

        $query = User::where('role', 'staff')
            ->when($currentHotel, fn($q) => $q->where('hotel_id', $currentHotel->id))
            ->when($department, fn($q) => $q->where('department', $department));

        $departments = (clone $query)->whereNotNull('department')->distinct()->pluck('department');

        $staffUnassigned = (clone $query)->whereNull('shift_id')->get();
        $staffShift1 = (clone $query)->where('shift_id', 1)->get();
        $staffShift2 = (clone $query)->where('shift_id', 2)->get();
        $staffShift3 = (clone $query)->where('shift_id', 3)->get();
        $shifts = Shift::all();

        return view('admin.shifts.index', compact('staffUnassigned', 'staffShift1', 'staffShift2', 'staffShift3', 'shifts', 'departments', 'department', 'hotelSlug'));
    }

    public function updateShift(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id', 'shift_id' => 'nullable|exists:shifts,id']);
        User::where('id', $request->user_id)->update(['shift_id' => $request->shift_id]);
        return response()->json(['success' => true]);
    }
}
