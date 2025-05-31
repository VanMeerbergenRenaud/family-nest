<?php

namespace App\Policies;

use App\Models\Family;
use App\Models\User;
use App\Services\FamilyRoleService;
use Illuminate\Auth\Access\HandlesAuthorization;

class FamilyPolicy
{
    use HandlesAuthorization;

    protected FamilyRoleService $familyRoleService;

    public function __construct(FamilyRoleService $familyRoleService)
    {
        $this->familyRoleService = $familyRoleService;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Family $family): bool
    {
        return $this->familyRoleService->isViewer($user, $family);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Family $family): bool
    {
        return $this->familyRoleService->isEditorOrAbove($user, $family);
    }

    public function delete(User $user, Family $family): bool
    {
        return $this->familyRoleService->isAdmin($user, $family);
    }

    public function inviteMembers(User $user, Family $family): bool
    {
        return $this->familyRoleService->isEditorOrAbove($user, $family);
    }

    public function manageMembers(User $user, Family $family): bool
    {
        return $this->familyRoleService->isAdmin($user, $family);
    }
}
