<?php

namespace App\Models;

use App\Enums\FamilyPermissionEnum;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
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

    // Return the value of the label corresponding to the permission of the user in his family
    public function getFamilyPermissionAttribute(): ?FamilyPermissionEnum
    {
        if (! $this->hasFamily()) {
            return null;
        }

        return FamilyPermissionEnum::tryFrom(
            $this->family()->pivot->permission
        );
    }

    public function isAdmin(): bool
    {
        return $this->getFamilyPermissionAttribute() === FamilyPermissionEnum::Admin;
    }

    /**
     * Configuration pour Algolia/Laravel Scout
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }

    public function scopeSearch($query, $searchTerm)
    {
        $searchTerm = trim(strtolower($searchTerm));

        return $query->where(function ($query) use ($searchTerm) {
            $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchTerm}%"]);
        });
    }
}
