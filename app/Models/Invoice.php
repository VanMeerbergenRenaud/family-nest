<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;

class Invoice extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'name', 'type', 'category', 'issuer_name', 'issuer_website',
        'amount', 'currency', 'paid_by', 'associated_members', 'issued_date', 'payment_due_date',
        'payment_reminder', 'payment_frequency', 'payment_status', 'payment_method', 'priority',
        'notes', 'tags', 'is_archived', 'is_favorite', 'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issued_date' => 'date',
        'payment_due_date' => 'date',
        'associated_members' => 'array',
        'tags' => 'array',
        'is_archived' => 'boolean',
        'is_favorite' => 'boolean',
    ];

    protected $with = ['file'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Obtenir le fichier principal associé à la facture
    public function file(): HasOne
    {
        return $this->hasOne(InvoiceFile::class)
            ->where('is_primary', true);
    }

    // Obtenir tous les fichiers associés à la facture
    public function files(): HasMany
    {
        return $this->hasMany(InvoiceFile::class);
    }

    // Configuration pour Algolia
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'reference' => $this->reference,
            'type' => $this->type,
            'category' => $this->category,
            'amount' => $this->amount,
            'issuer_name' => $this->issuer_name,
            'tags' => $this->tags,
        ];
    }

    // NB: Seules les factures non archivées peuvent être indexées
    public function shouldBeSearchable(): bool
    {
        return ! $this->is_archived;
    }
}
