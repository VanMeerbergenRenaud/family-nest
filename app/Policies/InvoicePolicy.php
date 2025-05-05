<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use App\Services\FamilyRoleService;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
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

    public function view(User $user, Invoice $invoice): bool
    {
        if (! $invoice->family_id) {
            return $user->id === $invoice->user_id;
        }

        if ($user->families()->where('family_id', $invoice->family_id)->doesntExist()) {
            return false;
        }

        return $this->familyRoleService->isViewer($user, $invoice->family);
    }

    public function addToFavorite(User $user, Invoice $invoice): bool
    {
        if (! $invoice->family_id) {
            return $user->id === $invoice->user_id;
        }

        if ($user->families()->where('family_id', $invoice->family_id)->doesntExist()) {
            return false;
        }

        return $user->id === $invoice->user_id;
    }

    public function create(User $user): bool
    {
        $family = $user->family();

        if (! $family) {
            return false;
        }

        return $this->familyRoleService->isEditorOrAbove($user, $family);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        if (! $invoice->family_id) {
            return $user->id === $invoice->user_id;
        }

        if ($user->families()->where('family_id', $invoice->family_id)->doesntExist()) {
            return false;
        }

        return $this->familyRoleService->isEditorOrAbove($user, $invoice->family);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        if (! $invoice->family_id) {
            return $user->id === $invoice->user_id;
        }

        if ($user->families()->where('family_id', $invoice->family_id)->doesntExist()) {
            return false;
        }

        return $this->familyRoleService->isAdmin($user, $invoice->family);
    }

    public function share(User $user, Invoice $invoice): bool
    {
        if (! $invoice->family_id) {
            return $user->id === $invoice->user_id;
        }

        if ($user->families()->where('family_id', $invoice->family_id)->doesntExist()) {
            return false;
        }

        return $this->familyRoleService->isEditorOrAbove($user, $invoice->family);
    }
}
