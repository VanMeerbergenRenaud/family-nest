<?php

namespace App\Models;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyInvitation extends Model implements ShouldQueue
{
    protected $fillable = [
        'family_id',
        'invited_by',
        'email',
        'token',
        'role',
        'relation',
        'is_admin',
        'expires_at',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'expires_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($invitation) {
            // By default, an invitation expires after 3 days
            if (! $invitation->expires_at) {
                $invitation->expires_at = now()->addDays(3);
            }
        });
    }

    // The family associated with the invitation
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    // The user who sent the invitation
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    // Verify if the invitation is still valid
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
