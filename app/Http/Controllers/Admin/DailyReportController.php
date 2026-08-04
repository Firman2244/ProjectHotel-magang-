<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\JsonResponse;

class DailyReportController extends Controller
{
    public function show(int $id): JsonResponse
    {
        $report = Report::with(['user', 'reportItems.task'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'html' => view('admin.reports.partials.detail-modal', compact('report'))->render()
        ]);
    }
}
