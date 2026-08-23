<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Shift;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $reqHotel = $request->query('hotel');
        $currentHotel = $reqHotel ? Hotel::find($reqHotel) : Hotel::first();
        $hotelId = $currentHotel?->id;
        $department = $request->query('department');

        $query = User::where('role', 'staff')
            ->select('id', 'name', 'email', 'department', 'shift_id', 'hotel_id')
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
            ->when($department, fn($q) => $q->where('department', $department));

        $departments = (clone $query)->whereNotNull('department')->distinct()->pluck('department');

        $staffs = (clone $query)->get();
        $staffUnassigned = $staffs->whereNull('shift_id');
        $staffShift1 = $staffs->where('shift_id', 1);
        $staffShift2 = $staffs->where('shift_id', 2);
        $staffShift3 = $staffs->where('shift_id', 3);

        $shifts = Shift::all();
        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $hotelSlug = $hotelId;

        return view('admin.shifts.index', compact('staffUnassigned', 'staffShift1', 'staffShift2', 'staffShift3', 'shifts', 'departments', 'department', 'hotelSlug', 'today'));
    }

    public function updateShift(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'nullable|exists:shifts,id'
        ]);

        $user = User::find($request->user_id);

        if ($user->shift_id != $request->shift_id) {
            $user->update([
                'shift_id' => $request->shift_id,
                'can_double_shift' => false,
                'double_shift_date' => null
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function grantDoubleShift(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $count = count($request->user_ids);

        User::whereIn('id', $request->user_ids)->update([
            'can_double_shift' => true,
            'double_shift_date' => $today
        ]);

        ActivityLog::record(Auth::id(), 'GRANT_DOUBLE_SHIFT', "Admin memberikan otorisasi Double Shift kepada $count staf.");

        return response()->json(['success' => true]);
    }

    public function updateConfig(Request $request)
    {
        $request->validate([
            'shifts' => 'required|array',
            'shifts.*.id' => 'required|exists:shifts,id',
            'shifts.*.start_time' => 'required|date_format:H:i',
            'shifts.*.end_time' => 'required|date_format:H:i',
            'shifts.*.deadline_time' => 'required|date_format:H:i',
        ]);

        foreach ($request->shifts as $shiftData) {
            Shift::where('id', $shiftData['id'])->update([
                'start_time' => $shiftData['start_time'],
                'end_time' => $shiftData['end_time'],
                'deadline_time' => $shiftData['deadline_time'],
            ]);
        }

        ActivityLog::record(Auth::id(), 'UPDATE_SHIFT_CONFIG', "Admin mengubah konfigurasi jam operasional shift.");

        return redirect()->back()->with('success', 'Konfigurasi jam operasional berhasil diperbarui!');
    }
}
