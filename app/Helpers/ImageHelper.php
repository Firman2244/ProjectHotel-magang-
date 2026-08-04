<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

if (!function_exists('getImageUrl')) {
    function getImageUrl(?string $path): string
    {
        if (empty($path)) {
            return asset('images/default-placeholder.png');
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        $cleanPath = ltrim($path, '/');
        if (file_exists(public_path($cleanPath))) {
            return asset($cleanPath);
        }

        if (file_exists(public_path('storage/' . $cleanPath))) {
            return asset('storage/' . $cleanPath);
        }

        return asset('storage/' . $cleanPath);
    }
}
