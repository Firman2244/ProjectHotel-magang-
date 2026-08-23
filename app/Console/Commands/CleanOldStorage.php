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

        $thresholdDate = Carbon::now()->subDays($setting->auto_delete_days)->timestamp;

        foreach (['reports', 'notes'] as $dir) {
            // [FIX BUG CRASH] Ganti 'files()' jadi 'allFiles()' agar bisa menelusuri sub-folder.
            $files = Storage::disk('public')->allFiles($dir);

            foreach ($files as $file) {
                if (Storage::disk('public')->lastModified($file) < $thresholdDate) {
                    Storage::disk('public')->delete($file);

                    if ($dir == 'notes') {
                        Note::where('image', $file)->update(['image' => null]);
                    } else {
                        ReportItem::where('before_image', $file)->update(['before_image' => null]);
                        ReportItem::where('after_image', $file)->update(['after_image' => null]);
                    }
                }
            }
        }
    }
}
