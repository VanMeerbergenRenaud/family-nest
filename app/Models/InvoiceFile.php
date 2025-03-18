<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_primary' => 'boolean',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    // Accesseur pour obtenir l'URL complète du fichier
    protected function filePath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('storage/'.$value),
        );
    }

    // Obtenir la taille formatée du fichier
    public function getFormattedFileSizeAttribute(): string
    {
        if (! $this->file_size) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $this->file_size > 0 ? floor(log($this->file_size, 1024)) : 0;

        return number_format($this->file_size / pow(1024, $power), 2).' '.$units[$power];
    }

    // Déterminer si le fichier est une image
    public function getIsImageAttribute(): bool
    {
        return in_array(strtolower($this->file_extension), ['jpg', 'jpeg', 'png', 'gif']);
    }

    // Déterminer si le fichier est un PDF
    public function getIsPdfAttribute(): bool
    {
        return strtolower($this->file_extension) === 'pdf';
    }

    // Déterminer si le fichier est un document Word
    public function getIsDocxAttribute(): bool
    {
        return strtolower($this->file_extension) === 'docx';
    }
}
