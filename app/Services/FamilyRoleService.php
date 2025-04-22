<?php

namespace App\Services;

use App\Enums\FamilyPermissionEnum;
use App\Models\Family;
use App\Models\User;

class FamilyRoleService
{
    /**
     * Checks if the user has the administrator role in the given family.
     * If no family is provided, it uses the user's default family.
     */
    public function isAdmin(User $user, ?Family $family = null): bool
    {
        $family = $family ?? $user->family();

        if (! $family) {
            return false;
        }

        return $user->families()
            ->where('family_id', $family->id)
            ->wherePivot('permission', FamilyPermissionEnum::Admin->value)
            ->exists();
    }

    /**
     * Checks if the user has the editor role or a higher role in the given family.
     * If no family is provided, it uses the user's default family.
     */
    public function isEditorOrAbove(User $user, ?Family $family = null): bool
    {
        $family = $family ?? $user->family();

        if (! $family) {
            return false;
        }

        return $user->families()
            ->where('family_id', $family->id)
            ->whereIn('permission', [FamilyPermissionEnum::Admin->value, FamilyPermissionEnum::Editor->value])
            ->exists();
    }

    /**
     * Checks if the user has at least the viewer role in the given family.
     * If no family is provided, it uses the user's default family.
     */
    public function isViewer(User $user, ?Family $family = null): bool
    {
        $family = $family ?? $user->family();

        if (! $family) {
            return false;
        }

        return $user->families()
            ->where('family_id', $family->id)
            ->exists();
    }
}
