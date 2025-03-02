<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;

class Invoice extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'file_path', 'name', 'type', 'category', 'issuer_name', 'issuer_website',
        'amount', 'paid_by', 'associated_members', 'issued_date', 'payment_due_date',
        'payment_reminder', 'payment_frequency', 'engagement_id', 'engagement_name',
        'payment_status', 'payment_method', 'priority', 'notes', 'tags', 'is_archived', 'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issued_date' => 'date',
        'payment_due_date' => 'date',
        'tags' => 'array',
        'is_archived' => 'boolean',
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
