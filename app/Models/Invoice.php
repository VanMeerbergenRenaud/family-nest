<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'issuer', 'type', 'category', 'website', 'amount', 'is_variable',
        'is_family_related', 'issued_date', 'payment_reminder', 'payment_frequency',
        'status', 'payment_method', 'priority', 'notes', 'tags', 'file_path',
        // Ajoutez les champs pour la répartition du montant et les engagements si nécessaire
    ];

    protected $casts = [
        'is_variable' => 'boolean',
        'is_family_related' => 'boolean',
        'issued_date' => 'date',
        'tags' => 'array', // Cast le champ JSON en tableau
    ];

    protected function filePath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('storage/'.$value),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function files(): hasOne
    {
        return $this->hasOne(InvoiceFile::class);
    }
}
