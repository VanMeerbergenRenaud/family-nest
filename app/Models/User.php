<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Scout\Searchable;
use Masmerise\Toaster\Toaster;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, Searchable;

    protected $fillable = ['name', 'email', 'password', 'avatar'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        try {
            if (Storage::disk('s3')->exists($this->avatar)) {
                return Storage::disk('s3')->temporaryUrl(
                    $this->avatar,
                    now()->addMinutes(20),
                    [
                        'ResponseContentType' => 'image/jpeg',
                        'ResponseContentDisposition' => 'inline',
                    ]
                );
            }
        } catch (\Exception $e) {
            Toaster::error('L‘avatar ne s‘est pas chargé correctement');
        }

        return null;
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function paidInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'paid_by_user_id');
    }

    // Members of the family
    public function families(): BelongsToMany
    {
        return $this->belongsToMany(Family::class)
            ->withPivot('permission', 'relation', 'is_admin')
            ->withTimestamps();
    }

    // Members of the family with admin permission
    public function family()
    {
        return $this->families()->first();
    }

    // Verify if the user is the admin of the family
    public function isAdminOfFamily(): bool
    {
        $family = $this->family();

        if (! $family) {
            return false;
        }

        return $this->families()->wherePivot('family_id', $family->id)
            ->wherePivot('is_admin', true)
            ->exists();
    }

    // Get the role of the user in the family
    public function getFamilyPermissionAttribute(): string
    {
        $family = $this->family();

        if (! $family) {
            return 'Administrateur';
        }

        $permission = $this->families()->wherePivot('family_id', $family->id)
            ->first()
            ->pivot
            ->permission;

        // Translate the permission to French
        return match ($permission) {
            'admin' => 'Administrateur',
            'editor' => 'Éditeur',
            'viewer' => 'Spectateur',
            default => ucfirst($permission),
        };
    }
}
