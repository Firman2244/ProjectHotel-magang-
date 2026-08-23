<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\StorageController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
        Route::delete('/avatar', 'deleteAvatar')->name('avatar.destroy');
    });

    Route::controller(ReportController::class)->prefix('reports')->name('reports.')->group(function () {
        Route::get('/history', 'history')->name('history');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{report}', 'show')->name('show');
        Route::put('/{report}/final', 'updateFinal')->name('updateFinal');
        Route::delete('/{report}', 'destroy')->name('destroy');
    });

    Route::delete('/report-items/{item}', [ReportController::class, 'destroyItem'])->name('reports.items.destroy');

    Route::controller(NoteController::class)->prefix('notes')->name('notes.')->group(function () {
        Route::post('/', 'store')->name('store');
        Route::post('/{note}/resolve', 'resolveTask')->name('resolve');
    });

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('hotels', HotelController::class);

        Route::resource('staff', StaffController::class);
        Route::get('/staff-scores', [StaffController::class, 'leaderboard'])->name('staff.scores');
        Route::get('/staff/{id}/point-history', [StaffController::class, 'pointHistoryModal'])->name('staff.points');

        Route::resource('tasks', TaskController::class);

        Route::controller(ShiftController::class)->prefix('shifts')->name('shifts.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/update', 'updateShift')->name('update');
            Route::post('/double-shift', 'grantDoubleShift')->name('grant_double_shift');
            Route::post('/config', 'updateConfig')->name('config');
        });

        Route::controller(AdminReportController::class)->prefix('reports')->name('reports.')->group(function () {
            Route::get('/summary', 'index')->name('summary');
            Route::get('/export', 'export')->name('export');
            Route::get('/{id}/detail', 'show')->name('detail');
        });

        Route::patch('/report-items/{item}/status', [ReportController::class, 'updateItemStatus']);
        Route::patch('/reports/{report}/verify-all', [ReportController::class, 'verifyAllTasks']);

        Route::controller(StorageController::class)->prefix('storage')->name('storage.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/settings', 'updateSettings')->name('settings');
            Route::delete('/clear', 'clearManual')->name('clear');
        });

        Route::controller(NoteController::class)->prefix('notes')->name('notes.')->group(function () {
            Route::get('/', 'indexAdmin')->name('index');
            Route::patch('/{note}/read', 'markAsRead')->name('read');
            Route::patch('/{note}/verify', 'verifyTask')->name('verify');
            Route::delete('/{note}', 'destroyAdmin')->name('destroy');
        });

        Route::controller(ActivityLogController::class)->prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::delete('/clear', 'clear')->name('clear');
        });
    });
});

require __DIR__.'/auth.php';
