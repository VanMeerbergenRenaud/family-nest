<?php

namespace App\Models;

use App\Enums\FamilyPermissionEnum;
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

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Get the temporary URL for the user's avatar
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
            Toaster::error('Le fichier n\'a pas pu être trouvé::Erreur de connexion au serveur.');
        }

        return null;
    }

    // Relationship: A user has many invoices
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Relationship: A user belongs to many families with additional pivot data
    public function families(): BelongsToMany
    {
        return $this->belongsToMany(Family::class)
            ->withPivot('permission', 'relation', 'is_admin')
            ->withTimestamps();
    }

    // Get the first family the user belongs to
    public function family()
    {
        return $this->families()->first();
    }

    // Check if the user has a family
    public function hasFamily(): bool
    {
        return $this->families()->exists();
    }

    public function getFamilyPermissionAttribute(): ?FamilyPermissionEnum
    {
        $family = $this->family();

        if (! $family) {
            return null;
        }

        $pivotData = $this->families()->where('family_id', $family->id)->first()->pivot ?? null;

        if (! $pivotData) {
            return null;
        }

        return FamilyPermissionEnum::tryFrom($pivotData->permission);
    }

    public function isAdmin(): bool
    {
        return $this->getFamilyPermissionAttribute() === FamilyPermissionEnum::Admin;
    }
}
