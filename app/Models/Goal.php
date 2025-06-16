<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'goal_type',
        'period_type',
        'start_date',
        'end_date',
        'is_recurring',
        'is_family_goal',
        'target_amount',
        'categories',
        'user_id',
        'family_id',
        'is_active',
        'is_completed',
        'current_amount',
        'completion_percentage',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
        'is_family_goal' => 'boolean',
        'target_amount' => 'decimal:2',
        'categories' => 'array',
        'is_active' => 'boolean',
        'is_completed' => 'boolean',
        'current_amount' => 'decimal:2',
        'completion_percentage' => 'decimal:2',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /* Pourcentage */
    public function getCurrentAmountAttribute()
    {
        $query = Invoice::query()
            ->whereBetween('issued_date', [$this->start_date, $this->end_date])
            ->whereIn('category', $this->categories);

        if ($this->is_family_goal) {
            if ($this->family_id) {
                $query->whereHas('user', fn ($q) => $q->where('family_id', $this->family_id));
            } else {
                return 0;
            }
        } else {
            if ($this->user_id) {
                $query->where('user_id', $this->user_id);
            } else {
                return 0;
            }
        }

        return $query->sum('amount');
    }

    public function getCompletionPercentageAttribute()
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        return min(100, round(($this->current_amount / $this->target_amount) * 100));
    }
}
