<?php

namespace App\Policies;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoalPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Goal $goal): bool
    {
        return $user->id === $goal->user_id || $goal->members->contains($user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->getFamilyPermissionAttribute()?->canEdit() ?? false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Goal $goal): bool
    {
        // Seul le créateur peut modifier
        return $user->id === $goal->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Goal $goal): bool
    {
        // Seul le créateur peut supprimer
        return $user->id === $goal->user_id;
    }
}
