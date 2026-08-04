<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportSummaryController;
use App\Http\Controllers\Admin\StorageController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/reports/history', [ReportController::class, 'history'])->name('reports.history');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::put('/reports/{report}/final', [ReportController::class, 'updateFinal'])->name('reports.updateFinal');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
    Route::delete('/report-items/{item}', [ReportController::class, 'destroyItem'])->name('reports.items.destroy');

    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        Route::resource('admin/hotels', HotelController::class)->names([
            'index' => 'admin.hotels.index',
            'create' => 'admin.hotels.create',
            'store' => 'admin.hotels.store',
            'edit' => 'admin.hotels.edit',
            'update' => 'admin.hotels.update',
            'destroy' => 'admin.hotels.destroy',
        ]);

        Route::resource('admin/staff', StaffController::class)->names([
            'index' => 'admin.staff.index',
            'create' => 'admin.staff.create',
            'store' => 'admin.staff.store',
            'edit' => 'admin.staff.edit',
            'update' => 'admin.staff.update',
            'destroy' => 'admin.staff.destroy',
        ]);

        Route::resource('admin/tasks', TaskController::class)->names([
            'index' => 'admin.tasks.index',
            'create' => 'admin.tasks.create',
            'store' => 'admin.tasks.store',
            'edit' => 'admin.tasks.edit',
            'update' => 'admin.tasks.update',
            'destroy' => 'admin.tasks.destroy',
        ]);

        Route::get('/admin/shifts', [ShiftController::class, 'index'])->name('admin.shifts.index');
        Route::post('/admin/shifts/update', [ShiftController::class, 'updateShift'])->name('admin.shifts.update');

        Route::get('/admin/reports/summary', [ReportSummaryController::class, 'index'])->name('admin.reports.summary');
        Route::get('/admin/reports/export', [ReportSummaryController::class, 'export'])->name('admin.reports.export');

        Route::get('/admin/storage', [StorageController::class, 'index'])->name('admin.storage.index');
        Route::post('/admin/storage/settings', [StorageController::class, 'updateSettings'])->name('admin.storage.settings');
        Route::delete('/admin/storage/clear', [StorageController::class, 'clearManual'])->name('admin.storage.clear');

        Route::get('/admin/notes', [NoteController::class, 'indexAdmin'])->name('admin.notes.index');
        Route::patch('/admin/notes/{note}/read', [NoteController::class, 'markAsRead'])->name('admin.notes.read');
        Route::delete('/admin/notes/{note}', [NoteController::class, 'destroyAdmin'])->name('admin.notes.destroy');

        Route::get('/admin/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity-logs');
    });
});

require __DIR__.'/auth.php';
