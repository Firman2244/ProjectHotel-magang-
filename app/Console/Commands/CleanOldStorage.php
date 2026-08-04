<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;
use App\Models\Note;
use App\Models\ReportItem;
use Carbon\Carbon;

class CleanOldStorage extends Command
{
    protected $signature = 'storage:clean-old';

    protected $description = 'Hapus gambar lama secara otomatis';

    public function handle()
    {
        $setting = Setting::find(1);

        if (!$setting || $setting->auto_delete_days == 0) {
            return;
        }

        $days = $setting->auto_delete_days;
        $thresholdDate = Carbon::now()->subDays($days)->timestamp;

        $directories = ['reports', 'notes'];

        foreach ($directories as $dir) {
            $files = Storage::disk('public')->files($dir);

            foreach ($files as $file) {
                $lastModified = Storage::disk('public')->lastModified($file);

                if ($lastModified < $thresholdDate) {
                    Storage::disk('public')->delete($file);

                    if ($dir == 'notes') {
                        Note::where('image', $file)->update(['image' => null]);
                    } elseif ($dir == 'reports') {
                        ReportItem::where('image', $file)->update(['image' => null]);
                    }
                }
            }
        }
    }
}
