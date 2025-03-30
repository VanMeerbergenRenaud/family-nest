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

    protected $fillable = ['name', 'email', 'password', 'avatar'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
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
}
