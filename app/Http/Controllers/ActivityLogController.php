<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $reqHotel = $request->query('hotel');
        $currentHotel = $reqHotel ? Hotel::find($reqHotel) : Hotel::first();
        $hotelId = $currentHotel?->id;

        $query = ActivityLog::with('user:id,name,role');

        if ($hotelId) {
            $query->whereHas('user', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.activity-logs', compact('logs'));
    }

    public function clear()
    {
        ActivityLog::truncate();

        ActivityLog::record(Auth::id(), 'CLEAR_LOGS', 'Sistem Peringatan: Admin ini telah mengosongkan seluruh riwayat Activity Log');

        return redirect()->back()->with('success', 'Seluruh riwayat Activity Log berhasil dibersihkan.');
    }
}
