<?php

namespace App\Traits;

use Illuminate\Support\Number;

trait FormatFileSizeTrait
{
    public function formatFileSize(int $bytes, int $precision = 2): string
    {
        return Number::fileSize($bytes, $precision);
    }
}
