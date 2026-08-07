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

    protected int $noteId;
    protected string $tempPath;
    protected int $hotelId;

    public function __construct(int $noteId, string $tempPath, int $hotelId)
    {
        $this->noteId   = $noteId;
        $this->tempPath = $tempPath;
        $this->hotelId  = $hotelId;
    }

    public function handle(): void
    {
        $note = Note::find($this->noteId);
        if (!$note || !Storage::disk('public')->exists($this->tempPath)) {
            return;
        }

        try {
            $monthFolder = Carbon::now('Asia/Jakarta')->format('Y-m');
            $folderPath  = "notes/hotel_{$this->hotelId}/{$monthFolder}";

            if (!Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->makeDirectory($folderPath);
            }

            $filename  = 'note_' . uniqid() . '_' . Str::random(5) . '.jpg';
            $finalPath = $folderPath . '/' . $filename;

            $absoluteTempPath = Storage::disk('public')->path($this->tempPath);
            $absoluteFinalPath = Storage::disk('public')->path($finalPath);

            $this->processWithNativePHP($absoluteTempPath, $absoluteFinalPath);

            $note->update(['image' => $finalPath]);

        } catch (Exception $e) {
            Log::error("Gagal memproses gambar Note ID {$this->noteId}: " . $e->getMessage());
        } finally {
            if (Storage::disk('public')->exists($this->tempPath)) {
                Storage::disk('public')->delete($this->tempPath);
            }
        }
    }

    private function processWithNativePHP(string $sourcePath, string $destinationPath): void
    {
        list($width, $height, $type) = getimagesize($sourcePath);
        $maxW = 800;

        if ($width > $maxW) {
            $newW = $maxW;
            $newH = (int) round($height * ($maxW / $width));
        } else {
            $newW = $width;
            $newH = $height;
        }

        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImg = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $srcImg = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                $srcImg = imagecreatefromwebp($sourcePath);
                break;
            default:
                copy($sourcePath, $destinationPath);
                return;
        }

        $dstImg = imagecreatetruecolor($newW, $newH);

        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
            $white = imagecolorallocate($dstImg, 255, 255, 255);
            imagefill($dstImg, 0, 0, $white);
        }

        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $width, $height);
        imagejpeg($dstImg, $destinationPath, 75);

        imagedestroy($srcImg);
        imagedestroy($dstImg);
    }
}
