<?php

namespace App\Models;

use App\Enums\CategoryEnum;
use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Enums\TypeEnum;
use App\Traits\HumanDateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;

class Invoice extends Model
{
    use HasFactory, HumanDateTrait, Searchable;

    protected $fillable = [
        'name', 'reference', 'type', 'category', 'issuer_name', 'issuer_website',
        'amount', 'currency', 'paid_by_user_id', 'family_id', 'issued_date',
        'payment_due_date', 'payment_reminder', 'payment_frequency',
        'payment_status', 'payment_method', 'priority', 'notes',
        'tags', 'is_archived', 'is_favorite', 'user_id',
    ];

    protected $casts = [
        'type' => TypeEnum::class,
        'category' => CategoryEnum::class,
        'amount' => 'decimal:2',
        'issued_date' => 'date',
        'payment_due_date' => 'date',
        'payment_reminder' => 'date',
        'payment_frequency' => PaymentFrequencyEnum::class,
        'payment_status' => PaymentStatusEnum::class,
        'payment_method' => PaymentMethodEnum::class,
        'priority' => PriorityEnum::class,
        'tags' => 'array',
        'is_archived' => 'boolean',
        'is_favorite' => 'boolean',
    ];

    protected $with = ['file', 'family'];

    protected $appends = [
        'has_shares',
        'total_percentage',
        'total_shared_amount',
        'is_fully_shared',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function file(): HasOne
    {
        return $this->hasOne(InvoiceFile::class)
            ->where('is_primary', true);
    }

    /**
     * Les partages associés à cette facture
     */
    public function sharings(): HasMany
    {
        return $this->hasMany(InvoiceSharing::class);
    }

    public function sharedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'invoice_sharings')
            ->withPivot('share_amount', 'share_percentage')
            ->withTimestamps();
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function paidByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by_user_id');
    }

    public function getHasSharesAttribute(): bool
    {
        if ($this->relationLoaded('sharings')) {
            return $this->sharings->isNotEmpty();
        }

        if (isset($this->attributes['sharings_count'])) {
            return $this->attributes['sharings_count'] > 0;
        }

        return $this->sharings()->exists();
    }

    public function getTotalPercentageAttribute(): float
    {
        if (isset($this->attributes['sharings_sum_share_percentage'])) {
            return (float) $this->attributes['sharings_sum_share_percentage'];
        }

        if ($this->relationLoaded('sharings')) {
            return $this->sharings->sum('share_percentage');
        }

        return $this->sharings()->sum('share_percentage');
    }

    public function getTotalSharedAmountAttribute(): float
    {
        if (isset($this->attributes['sharings_sum_share_amount'])) {
            return (float) $this->attributes['sharings_sum_share_amount'];
        }

        if ($this->relationLoaded('sharings')) {
            return $this->sharings->sum('share_amount');
        }

        return $this->sharings()->sum('share_amount');
    }

    public function getIsFullySharedAttribute(): bool
    {
        return $this->total_percentage >= 99.9 ||
            (floatval($this->amount) > 0 && abs(floatval($this->amount) - $this->total_shared_amount) < 0.01);
    }

    /* Algolia */
    public function toSearchableArray(): array
    {
        if (! $this->relationLoaded('family')) {
            $this->load('family');
        }

        return [
            'name' => $this->name,
            'reference' => $this->reference,
            'type' => $this->type?->value,
            'category' => $this->category?->value,
            'issuer_name' => $this->issuer_name,
            'tags' => $this->tags,
            'amount' => (float) $this->amount,
            'user_id' => $this->user_id,
            'family_id' => $this->family_id,
        ];
    }

    public function scopeSearch($query, $searchTerm)
    {
        $searchTerm = strtolower($searchTerm);

        return $query->where(function ($query) use ($searchTerm) {
            $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                ->orWhereRaw('LOWER(reference) LIKE ?', ["%{$searchTerm}%"])
                ->orWhereRaw('LOWER(type) LIKE ?', ["%{$searchTerm}%"])
                ->orWhereRaw('LOWER(category) LIKE ?', ["%{$searchTerm}%"])
                ->orWhereRaw('LOWER(issuer_name) LIKE ?', ["%{$searchTerm}%"])
                ->orWhereRaw('LOWER(tags::text) LIKE ?', ["%{$searchTerm}%"])
                ->orWhere('amount', 'LIKE', "%{$searchTerm}%");
        });
    }
}
