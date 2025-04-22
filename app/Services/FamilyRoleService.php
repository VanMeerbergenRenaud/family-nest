<?php

namespace App\Services;

use App\Enums\FamilyPermissionEnum;
use App\Models\Family;
use App\Models\User;
use Illuminate\Support\Collection;

class FamilyRoleService
{
    protected array $permissionsCache = [];

    /**
     * Checks if the user has the administrator role in the given family.
     * If no family is provided, it uses the user's default family.
     */
    protected function getUserFamilyPermissions(User $user, Family $family): Collection
    {
        $cacheKey = $user->id.'_'.$family->id;

        if (! isset($this->permissionsCache[$cacheKey])) {
            $this->permissionsCache[$cacheKey] = $user->families()
                ->where('family_id', $family->id)
                ->pluck('permission');
        }

        return $this->permissionsCache[$cacheKey];
    }

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

        $permissions = $this->getUserFamilyPermissions($user, $family);

        return $permissions->contains(FamilyPermissionEnum::Admin->value);
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

        $permissions = $this->getUserFamilyPermissions($user, $family);

        return $permissions->contains(FamilyPermissionEnum::Admin->value) ||
               $permissions->contains(FamilyPermissionEnum::Editor->value);
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

        $permissions = $this->getUserFamilyPermissions($user, $family);

        return $permissions->isNotEmpty();
    }
}
