<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $query = ActivityLog::with('user');

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.activity-logs', compact('logs'));
    }

    public function clear()
    {
        if (Auth::user()->role !== 'admin') abort(403);

        ActivityLog::truncate();

        ActivityLog::record(Auth::id(), 'CLEAR_LOGS', 'Sistem Peringatan: Admin ini telah mengosongkan seluruh riwayat Activity Log');

        return redirect()->back()->with('success', 'Seluruh riwayat Activity Log berhasil dibersihkan.');
    }
}
