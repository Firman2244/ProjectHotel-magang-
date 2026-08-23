<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\ReportItem;
use App\Models\Note;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    private function getSetting()
    {
        return Setting::firstOrCreate(
            ['id' => 1],
            ['key' => 'storage_config', 'auto_delete_days' => 0]
        );
    }

    public function index()
    {
        $setting = $this->getSetting();
        $autoDeleteDays = $setting->auto_delete_days;

        $allFiles = [];
        $disk = Storage::disk('public');

        foreach (['reports', 'notes'] as $dir) {
            if ($disk->exists($dir)) {
                $allFiles = array_merge($allFiles, $disk->allFiles($dir));
            }
        }

        $totalBytes = 0;
        $images = [];

        foreach ($allFiles as $file) {
            if (str_contains($file, 'reports/tmp')) continue;

            $size = $disk->size($file);
            $totalBytes += $size;
            $images[] = [
                'url' => asset('storage/' . $file),
                'size' => round($size / 1024, 2),
                'last_modified' => $disk->lastModified($file)
            ];
        }

        usort($images, fn($a, $b) => $b['last_modified'] <=> $a['last_modified']);

        $totalSizeMb = round($totalBytes / 1048576, 2);

        return view('admin.storage.index', compact('totalSizeMb', 'images', 'autoDeleteDays'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'auto_delete_days' => 'required|integer|min:0'
        ]);

        $setting = $this->getSetting();
        $setting->auto_delete_days = $request->auto_delete_days;
        $setting->save();

        return redirect()->back()->with('success', 'Pengaturan auto-delete berhasil diperbarui!');
    }

    public function clearManual()
    {
        $disk = Storage::disk('public');

        foreach (['reports', 'notes'] as $dir) {
            if ($disk->exists($dir)) {
                $files = $disk->allFiles($dir);
                foreach ($files as $file) {
                    if (!str_contains($file, 'reports/tmp')) {
                        $disk->delete($file);
                    }
                }
            }
        }

        ReportItem::query()->update([
            'before_image' => null,
            'after_image' => null
        ]);

        Note::query()->update(['image' => null]);

        return redirect()->back()->with('success', 'Seluruh gambar fisik dan data di sistem berhasil dihapus bersih!');
    }
}
