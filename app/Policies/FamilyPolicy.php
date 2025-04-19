<?php

namespace App\Policies;

use App\Enums\FamilyPermissionEnum;
use App\Models\Family;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FamilyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->families()->exists();
    }

    public function view(User $user, Family $family): bool
    {
        return $family->users()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->families()->exists();
    }

    public function update(User $user, Family $family): bool
    {
        return $family->users()
            ->where('user_id', $user->id)
            ->whereIn('permission', [
                FamilyPermissionEnum::Admin->value,
                FamilyPermissionEnum::Editor->value,
            ])
            ->exists();
    }

    public function delete(User $user, Family $family): bool
    {
        return $family->users()
            ->where('user_id', $user->id)
            ->where('permission', FamilyPermissionEnum::Admin->value)
            ->exists();
    }

    public function inviteMembers(User $user, Family $family): bool
    {
        return $family->users()
            ->where('user_id', $user->id)
            ->whereIn('permission', [
                FamilyPermissionEnum::Admin->value,
                FamilyPermissionEnum::Editor->value,
            ])
            ->exists();
    }

    public function manageMembers(User $user, Family $family): bool
    {
        return $family->users()
            ->where('user_id', $user->id)
            ->where('permission', FamilyPermissionEnum::Admin->value)
            ->exists();
    }
}
