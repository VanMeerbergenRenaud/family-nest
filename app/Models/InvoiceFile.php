<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'file_path',
        'file_name',
        'file_extension',
        'file_size',
        'is_primary',
        'compression_status',
        'original_size',
        'compression_rate',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_primary' => 'boolean',
        'original_size' => 'integer',
        'compression_rate' => 'float',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
