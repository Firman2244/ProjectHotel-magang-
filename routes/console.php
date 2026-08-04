<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use App\Models\Note;
use App\Models\Report;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $setting = Setting::find(1);

    if ($setting && $setting->auto_delete_days > 0) {
        $days = $setting->auto_delete_days;
        $thresholdDate = Carbon::now()->subDays($days)->timestamp;
        $directories = ['reports', 'notes'];

        foreach ($directories as $dir) {
            $files = Storage::disk('public')->files($dir);

            foreach ($files as $file) {
                $lastModified = Storage::disk('public')->lastModified($file);

                if ($lastModified < $thresholdDate) {
                    Storage::disk('public')->delete($file);

                    if ($dir === 'notes') {
                        Note::where('image', $file)->update(['image' => null]);
                    } elseif ($dir === 'reports' && class_exists(Report::class) && Schema::hasColumn('reports', 'image')) {
                        Report::where('image', $file)->update(['image' => null]);
                    }
                }
            }
        }
    }
})->dailyAt('00:00');
