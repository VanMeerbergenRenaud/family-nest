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

    /**
     * Checks if the user has a specific role in the given family.
     * If no family is provided, it uses the user's default family.
     */
    public function hasRole(User $user, string $role, ?Family $family = null): bool
    {
        $family = $family ?? $user->family();

        if (! $family) {
            return false;
        }

        // Validate role with enum
        $permission = FamilyPermissionEnum::tryFrom($role);
        if (! $permission) {
            return false;
        }

        return $user->families()
            ->where('family_id', $family->id)
            ->wherePivot('permission', $permission->value)
            ->exists();
    }

    /**
     * Retrieves the user's role in the given family.
     * If no family is provided, it uses the user's default family.
     * Returns null if the user is not part of the family.
     */
    public function getUserRole(User $user, ?Family $family = null): ?string
    {
        $family = $family ?? $user->family();

        if (! $family) {
            return null;
        }

        $familyUser = $user->families()
            ->where('family_id', $family->id)
            ->first();

        return $familyUser ? $familyUser->pivot->permission : null;
    }

    /**
     * Retrieves the user's role as an enum in the given family.
     * If no family is provided, it uses the user's default family.
     * Returns null if the user is not part of the family.
     */
    public function getUserPermissionEnum(User $user, ?Family $family = null): ?FamilyPermissionEnum
    {
        $role = $this->getUserRole($user, $family);

        if (! $role) {
            return null;
        }

        return FamilyPermissionEnum::tryFrom($role);
    }
}
