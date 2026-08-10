<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    private function getSetting()
    {
        $setting = Setting::find(1);
        
        if (!$setting) {
            $setting = new Setting();
            $setting->id = 1;
            $setting->key = 'storage_config';
            $setting->auto_delete_days = 0;
            $setting->save();
        }
        
        return $setting;
    }

    public function index()
    {
        $setting = $this->getSetting();
        $autoDeleteDays = $setting->auto_delete_days;

        $allFiles = [];
        
        if (Storage::disk('public')->exists('reports')) {
            $allFiles = array_merge($allFiles, Storage::disk('public')->allFiles('reports'));
        }
        if (Storage::disk('public')->exists('notes')) {
            $allFiles = array_merge($allFiles, Storage::disk('public')->allFiles('notes'));
        }

        $totalBytes = 0;
        $images = [];

        foreach ($allFiles as $file) {
            if (str_contains($file, 'reports/tmp')) continue;

            $size = Storage::disk('public')->size($file);
            $totalBytes += $size;
            $images[] = [
                'url' => asset('storage/' . $file),
                'size' => round($size / 1024, 2),
                'last_modified' => Storage::disk('public')->lastModified($file)
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
        $directories = ['reports', 'notes'];
        
        foreach ($directories as $dir) {
            if (Storage::disk('public')->exists($dir)) {
                $files = Storage::disk('public')->allFiles($dir);
                foreach ($files as $file) {
                    if (!str_contains($file, 'reports/tmp')) {
                        Storage::disk('public')->delete($file);
                    }
                }
            }
        }
        
        return redirect()->back()->with('success', 'Seluruh gambar berhasil dihapus secara permanen!');
    }
}