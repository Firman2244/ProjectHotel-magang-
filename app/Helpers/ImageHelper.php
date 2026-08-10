<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

if (!function_exists('getImageUrl')) {
    function getImageUrl(?string $path): string
    {
        if (empty($path)) return asset('images/default-placeholder.png');
        if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
        if (Storage::disk('public')->exists($path)) return asset('storage/' . $path);

        $cleanPath = ltrim($path, '/');
        if (file_exists(public_path($cleanPath))) return asset($cleanPath);
        return asset('storage/' . $cleanPath);
    }
}

if (!function_exists('processImage')) {
    function processImage(string $sourcePath, string $destinationPath, int $maxW = 1200): void
    {
        if (!extension_loaded('gd')) {
            copy($sourcePath, $destinationPath);
            return;
        }

        list($width, $height, $type) = getimagesize($sourcePath);
        $newW = min($width, $maxW);
        $newH = (int) round($height * ($newW / $width));

        $srcImg = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => imagecreatefromwebp($sourcePath),
            default => null,
        };

        if (!$srcImg) {
            copy($sourcePath, $destinationPath);
            return;
        }

        $dstImg = imagecreatetruecolor($newW, $newH);

        if (in_array($type, [IMAGETYPE_PNG, IMAGETYPE_WEBP])) {
            $white = imagecolorallocate($dstImg, 255, 255, 255);
            imagefill($dstImg, 0, 0, $white);
        }

        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $width, $height);
        imagejpeg($dstImg, $destinationPath, 80);

        imagedestroy($srcImg);
        imagedestroy($dstImg);
    }
}
