<?php

namespace App\Traits;

trait FormatSizeTrait
{
    /**
     * Format a file size in bytes to a human-readable string
     */
    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);

        $base = floor(($bytes ? log($bytes) : 0) / log(1024));
        $base = min($base, count($units) - 1);

        $bytes /= pow(1024, $base);

        return round($bytes, 2).' '.$units[$base];
    }
}
