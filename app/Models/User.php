<?php

namespace App\Models;

use App\Enums\FamilyPermissionEnum;
use App\Mail\CustomVerifyEmail;
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
        'auth_provider'
    ];

    protected $familyRelation = null;

    protected $hasFamily = null;

    protected $familyMembership = [];

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
            Toaster::error('Votre photo de profil n\'a pas pu être trouvée::Erreur de connexion internet au serveur.');
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

    // Get the first family of the user
    public function family()
    {
        if ($this->familyRelation === null) {
            $this->familyRelation = $this->families()->first();
        }

        return $this->familyRelation;
    }

    // Check if the user has a family
    public function hasFamily(): bool
    {
        if ($this->hasFamily === null) {
            if ($this->relationLoaded('families')) {
                $this->hasFamily = $this->families->isNotEmpty();
            } else {
                $this->hasFamily = $this->families()->exists();
            }
        }

        return $this->hasFamily;
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

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        if ($this->email_verified_at === null) {
            $this->notify(new CustomVerifyEmail);
        }
    }

    /**
     * Get all invoices accessible to this user (personal and family invoices)
     */
    public function accessibleInvoices()
    {
        // If no family, only return personal invoices
        if (! $this->hasFamily()) {
            return $this->invoices();
        }

        $familyIds = $this->families()->pluck('families.id')->toArray();

        return Invoice::where(function ($query) use ($familyIds) {
            $query->where('user_id', $this->id)
                ->orWhereIn('family_id', $familyIds);
        });
    }

    /**
     * Vérifie si l'utilisateur appartient à la famille spécifiée
     */
    public function belongsToFamily(?int $familyId): bool
    {
        if (empty($familyId)) {
            return false;
        }

        // Vérifier si le résultat est déjà en cache
        if (! isset($this->familyMembership[$familyId])) {
            $this->familyMembership[$familyId] = $this->families()
                ->where('family_id', $familyId)
                ->exists();
        }

        return $this->familyMembership[$familyId];
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
