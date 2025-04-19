<?php

namespace App\Models;

use App\Jobs\SendFamilyInvitation;
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
        'permission',
        'relation',
        'is_admin',
        'expires_at',
        'send_failed',
        'send_error',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'expires_at' => 'datetime',
        'send_failed' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($invitation) {
            if (! $invitation->expires_at) {
                // By default, an invitation expires after 3 days
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

    // Resend the invitation
    public function resend(): void
    {
        // Reset the failure status
        $this->update([
            'send_failed' => false,
            'send_error' => null,
        ]);

        // Reset expiration date
        $this->update([
            'expires_at' => now()->addDays(3),
        ]);

        // Dispatch the job to send the invitation
        SendFamilyInvitation::dispatch(
            $this,
            $this->family,
            $this->inviter
        );
    }
}
