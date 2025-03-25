<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Les utilisateurs qui appartiennent à cette famille.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('permission', 'relation', 'is_admin')
            ->withTimestamps();
    }

    /**
     * Les administrateurs de la famille.
     */
    public function admins()
    {
        return $this->users()->wherePivot('is_admin', true);
    }

    /**
     * Les factures associées à cette famille.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
