<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    // Relations
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_admin')
            ->withTimestamps();
    }
}
