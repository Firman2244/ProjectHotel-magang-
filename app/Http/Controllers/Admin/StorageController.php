<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Note;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class StorageController extends Controller
{
    public function index()
    {
        $setting = Setting::firstOrCreate(['id' => 1], ['auto_delete_days' => 0]);
        $autoDeleteDays = $setting->auto_delete_days;

        // PERBAIKAN: Gunakan allFiles() agar Laravel membaca isi dalam sub-folder (hotel/tanggal)
        $reportFiles = Storage::disk('public')->allFiles('reports');
        $noteFiles = Storage::disk('public')->allFiles('notes');

        $allFiles = array_merge($reportFiles, $noteFiles);

        $totalBytes = 0;
        $images = [];

        foreach ($allFiles as $file) {
            // Opsional: Sembunyikan file dari folder 'tmp' agar tidak muncul di galeri
            if (strpos($file, 'reports/tmp') !== false) {
                continue;
            }

            $size = Storage::disk('public')->size($file);
            $totalBytes += $size;
            $images[] = [
                'url' => asset('storage/' . $file),
                'size' => round($size / 1024, 2),
                'last_modified' => Storage::disk('public')->lastModified($file),
            ];
        }

        $totalSizeMb = round($totalBytes / (1024 * 1024), 2);

        return view('admin.storage.index', compact('totalSizeMb', 'images', 'autoDeleteDays'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'auto_delete_days' => 'required|integer|min:0'
        ]);

        $setting = Setting::find(1);
        if (!$setting) {
            $setting = new Setting();
            $setting->id = 1;
        }

        $setting->auto_delete_days = $request->auto_delete_days;
        $setting->save();

        return redirect()->back()->with('success', 'Pengaturan hapus otomatis berhasil diperbarui!');
    }

    public function clearManual()
    {
        Storage::disk('public')->deleteDirectory('reports');
        Storage::disk('public')->deleteDirectory('notes');
        Storage::disk('public')->makeDirectory('reports');
        Storage::disk('public')->makeDirectory('notes');

        Note::whereNotNull('image')->update(['image' => null]);

        if (class_exists(Report::class) && Schema::hasColumn('reports', 'image')) {
            Report::whereNotNull('image')->update(['image' => null]);
        }

        return redirect()->back()->with('success', 'Semua gambar berhasil dihapus permanen dari server!');
    }
}
