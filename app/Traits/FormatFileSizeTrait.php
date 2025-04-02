<?php

namespace App\Traits;

use Illuminate\Support\Number;

trait FormatFileSizeTrait
{
    /**
     * Format a file size in bytes to a human-readable string
     */
    public function formatFileSize(int $bytes, int $precision = 2): string
    {
        return Number::fileSize($bytes, $precision);
    }
}
