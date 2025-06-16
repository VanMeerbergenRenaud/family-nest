<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceSharing extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'share_amount',
        'share_percentage',
    ];

    protected $casts = [
        'share_amount' => 'decimal:2',
        'share_percentage' => 'decimal:2',
    ];

    /**
     * Obtenir l'utilisateur associé à cette part.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtenir la facture associée à cette part.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Détermine si cet utilisateur est le payeur de la facture
     * Optimisé pour éviter une requête inutile
     */
    public function isPayer(): bool
    {
        if ($this->relationLoaded('invoice')) {
            return $this->user_id === $this->invoice->paid_by_user_id;
        }

        return $this->user_id === Invoice::where('id', $this->invoice_id)->value('paid_by_user_id');
    }

    /**
     * Formatage du montant pour l'affichage
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->share_amount, 2, ',', ' ');
    }

    /**
     * Formatage du pourcentage pour l'affichage
     */
    public function getFormattedPercentageAttribute(): string
    {
        return number_format($this->share_percentage, 0);
    }
}
