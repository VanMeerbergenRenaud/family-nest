<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class Invoice extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'file_path', 'file_size', 'name', 'type', 'category', 'issuer_name', 'issuer_website',
        'amount', 'currency', 'paid_by', 'associated_members', 'issued_date', 'payment_due_date',
        'payment_reminder', 'payment_frequency', 'engagement_id', 'engagement_name',
        'payment_status', 'payment_method', 'priority', 'notes', 'tags', 'is_archived', 'is_favorite', 'user_id',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'amount' => 'decimal:2',
        'issued_date' => 'date',
        'payment_due_date' => 'date',
        'tags' => 'array',
        'is_archived' => 'boolean',
        'is_favorite' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function filePath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('storage/'.$value),
        );
    }

    // Get formatted file size
    public function getFormattedFileSizeAttribute(): string
    {
        if (! $this->file_size) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $this->file_size > 0 ? floor(log($this->file_size, 1024)) : 0;

        return number_format($this->file_size / pow(1024, $power), 2).' '.$units[$power];
    }

    // Scope pour récupérer les factures archivées
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    // Scope pour récupérer les factures non archivées (actives)
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    // Configuration pour Algolia
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'reference' => $this->reference,
            'type' => $this->type,
            'category' => $this->category,
            'issuer_name' => $this->issuer_name,
            'tags' => $this->tags,
            'is_archived' => $this->is_archived,
        ];
    }

    // NB: Seules les factures non archivées peuvent être indexées
    public function shouldBeSearchable(): bool
    {
        return !$this->is_archived;
    }
}
