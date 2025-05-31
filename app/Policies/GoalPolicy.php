<?php

namespace App\Policies;

use App\Models\Goal;
use App\Models\User;
use App\Services\FamilyRoleService;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoalPolicy
{
    use HandlesAuthorization;

    protected FamilyRoleService $familyRoleService;

    public function __construct(FamilyRoleService $familyRoleService)
    {
        $this->familyRoleService = $familyRoleService;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasFamily();
    }

    public function view(User $user, Goal $goal): bool
    {
        // L'utilisateur peut voir s'il est le créateur ou membre du goal
        if ($user->id === $goal->user_id || $goal->members->contains($user->id)) {
            return true;
        }

        // Si le goal est lié à une famille, vérifier les permissions familiales
        if ($goal->family_id && $user->belongsToFamily($goal->family_id)) {
            return $this->familyRoleService->isViewer($user, $goal->family);
        }

        return false;
    }

    public function create(User $user): bool
    {
        $family = $user->family();

        if (!$family) {
            return false;
        }

        return $this->familyRoleService->isEditorOrAbove($user, $family);
    }

    public function update(User $user, Goal $goal): bool
    {
        // Seul le créateur peut modifier
        if ($user->id === $goal->user_id) {
            return true;
        }

        // Un administrateur de famille peut également modifier
        if ($goal->family_id && $user->belongsToFamily($goal->family_id)) {
            return $this->familyRoleService->isAdmin($user, $goal->family);
        }

        return false;
    }

    public function delete(User $user, Goal $goal): bool
    {
        // Seul le créateur peut supprimer
        if ($user->id === $goal->user_id) {
            return true;
        }

        // Un administrateur de famille peut également supprimer
        if ($goal->family_id && $user->belongsToFamily($goal->family_id)) {
            return $this->familyRoleService->isAdmin($user, $goal->family);
        }

        return false;
    }
}
