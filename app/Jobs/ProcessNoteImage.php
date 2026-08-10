<?php

namespace App\Jobs;

use App\Models\Note;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Exception;

class ProcessNoteImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected int $noteId, protected string $tempPath, protected int $hotelId) {}

    public function handle(): void
    {
        $note = Note::find($this->noteId);
        if (!$note || !Storage::disk('public')->exists($this->tempPath)) return;

        try {
            $folderPath  = "notes/hotel_{$this->hotelId}/" . Carbon::now('Asia/Jakarta')->format('Y-m');
            if (!Storage::disk('public')->exists($folderPath)) Storage::disk('public')->makeDirectory($folderPath);

            $finalPath = $folderPath . '/note_' . uniqid() . '_' . Str::random(5) . '.jpg';

            \App\Helpers\processImage(Storage::disk('public')->path($this->tempPath), Storage::disk('public')->path($finalPath), 800);

            $note->update(['image' => $finalPath]);
        } catch (Exception $e) {
            Log::error("Gagal memproses gambar Note ID {$this->noteId}: " . $e->getMessage());
        } finally {
            if (Storage::disk('public')->exists($this->tempPath)) Storage::disk('public')->delete($this->tempPath);
        }
    }
}
