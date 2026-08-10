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

    public function __construct(protected int $itemId, protected string $imageType, protected string $tempPath, protected ?int $hotelId) {}

    public function handle(): void
    {
        if (!Storage::disk('public')->exists($this->tempPath)) return;

        $item = ReportItem::find($this->itemId);
        if (!$item) {
            Storage::disk('public')->delete($this->tempPath);
            return;
        }

        try {
            $directory = "reports/" . ($this->hotelId ? "hotel_{$this->hotelId}" : "hotel_general") . "/" . Carbon::now()->format('Y-m');
            if (!Storage::disk('public')->exists($directory)) Storage::disk('public')->makeDirectory($directory);

            $finalPath = "{$directory}/" . uniqid('rpt_') . '.jpg';

            \App\Helpers\processImage(Storage::disk('public')->path($this->tempPath), Storage::disk('public')->path($finalPath), 1200);

            $item->update([$this->imageType => $finalPath]);
            Storage::disk('public')->delete($this->tempPath);
        } catch (\Exception $e) {
            Log::error("Job Compress Image Gagal: " . $e->getMessage());
        }
    }
}
