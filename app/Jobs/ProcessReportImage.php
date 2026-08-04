<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\ReportItem;
use Carbon\Carbon;

class ProcessReportImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    protected int $itemId;
    protected string $imageType;
    protected string $tempPath;
    protected ?int $hotelId;

    public function __construct(int $itemId, string $imageType, string $tempPath, ?int $hotelId)
    {
        $this->itemId = $itemId;
        $this->imageType = $imageType;
        $this->tempPath = $tempPath;
        $this->hotelId = $hotelId;
    }

    public function handle(): void
    {
        if (!Storage::disk('public')->exists($this->tempPath)) {
            return;
        }

        $item = ReportItem::find($this->itemId);
        if (!$item) {
            Storage::disk('public')->delete($this->tempPath);
            return;
        }

        try {
            $absoluteTempPath = Storage::disk('public')->path($this->tempPath);
            $dateFolder = Carbon::now()->format('Y-m');
            $hotelFolder = $this->hotelId ? "hotel_{$this->hotelId}" : "hotel_general";
            $directory = "reports/{$hotelFolder}/{$dateFolder}";

            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            $fileName = uniqid('rpt_') . '.jpg';
            $finalPath = "{$directory}/{$fileName}";
            $absoluteFinalPath = Storage::disk('public')->path($finalPath);

            $this->processWithNativePHP($absoluteTempPath, $absoluteFinalPath);

            $item->update([
                $this->imageType => $finalPath
            ]);

            Storage::disk('public')->delete($this->tempPath);

        } catch (\Exception $e) {
            Log::error("Job Compress Image Gagal: " . $e->getMessage());
        }
    }

    private function processWithNativePHP(string $sourcePath, string $destinationPath): void
    {
        list($width, $height, $type) = getimagesize($sourcePath);
        $maxW = 1200;

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
        imagejpeg($dstImg, $destinationPath, 80);

        imagedestroy($srcImg);
        imagedestroy($dstImg);
    }
}
