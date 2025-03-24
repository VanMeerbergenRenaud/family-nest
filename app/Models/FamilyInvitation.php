<?php

namespace App\Models;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyInvitation extends Model implements ShouldQueue
{
    use HasFactory;

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invitation) {
            // Par défaut, une invitation expire après 7 jours
            if (! $invitation->expires_at) {
                $invitation->expires_at = now()->addDays(7);
            }
        });
    }

    /**
     * La famille qui a envoyé l'invitation.
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * L'utilisateur qui a envoyé l'invitation.
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Vérifie si l'invitation a expiré.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
