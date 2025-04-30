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

    protected $with = ['file', 'family', 'sharedUsers'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function file(): HasOne
    {
        return $this->hasOne(InvoiceFile::class)
            ->where('is_primary', true);
    }

    public function sharedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'invoice_user')
            ->withPivot('share_amount', 'share_percentage')
            ->withTimestamps();
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * Configuration Algolia
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'reference' => $this->reference,
            'type' => $this->type,
            'category' => $this->category,
            'issuer_name' => $this->issuer_name,
            'tags' => $this->tags,
            'amount' => (float) $this->amount,
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
