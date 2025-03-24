<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\Searchable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Les factures créées par l'utilisateur.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Les factures payées par l'utilisateur.
     */
    public function paidInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'paid_by_user_id');
    }

    /**
     * Les familles dont l'utilisateur est membre.
     */
    public function families(): BelongsToMany
    {
        return $this->belongsToMany(Family::class)
            ->withPivot('permission', 'relation', 'is_admin')
            ->withTimestamps();
    }

    /**
     * La famille principale de l'utilisateur.
     * Un utilisateur ne peut appartenir qu'à une seule famille,
     * mais cette méthode aide à clarifier l'intention.
     */
    public function family()
    {
        return $this->families()->first();
    }

    /**
     * Vérifie si l'utilisateur est administrateur de sa famille.
     */
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
}
