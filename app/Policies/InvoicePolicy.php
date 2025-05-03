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
        // If no family attached to invoice, only creator can view
        if (! $invoice->family_id) {
            return $user->id === $invoice->user_id;
        }

        // Check if user belongs to the same family as the invoice
        if ($user->families()->where('family_id', $invoice->family_id)->doesntExist()) {
            return false;
        }

        // Family members with at least Viewer permission can see invoices
        return $this->familyRoleService->isViewer($user, $invoice->family);
    }

    public function create(User $user): bool
    {
        $family = $user->family();

        if (! $family) {
            return false;
        }

        // Editors and Admins can create invoices
        return $this->familyRoleService->isEditorOrAbove($user, $family);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        // If no family attached to invoice, only creator can update
        if (! $invoice->family_id) {
            return $user->id === $invoice->user_id;
        }

        // Check if user belongs to the same family as the invoice
        if ($user->families()->where('family_id', $invoice->family_id)->doesntExist()) {
            return false;
        }

        // Family Editors and Admins can update invoices
        return $this->familyRoleService->isEditorOrAbove($user, $invoice->family);
    }

    public function archive(User $user, Invoice $invoice): bool
    {
        // If no family attached to invoice, only creator can archive
        if (! $invoice->family_id) {
            return $user->id === $invoice->user_id;
        }

        // Check if user belongs to the same family as the invoice
        if ($user->families()->where('family_id', $invoice->family_id)->doesntExist()) {
            return false;
        }

        // Only family Admins can archive family invoices
        return $this->familyRoleService->isAdmin($user, $invoice->family);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        // If no family attached to invoice, only creator can delete
        if (! $invoice->family_id) {
            return $user->id === $invoice->user_id;
        }

        // Check if user belongs to the same family as the invoice
        if ($user->families()->where('family_id', $invoice->family_id)->doesntExist()) {
            return false;
        }

        // Only family Admins can delete family invoices
        return $this->familyRoleService->isAdmin($user, $invoice->family);
    }

    public function share(User $user, Invoice $invoice): bool
    {
        // If no family attached to invoice, only creator can share
        if (! $invoice->family_id) {
            return $user->id === $invoice->user_id;
        }

        // Check if user belongs to the same family as the invoice
        if ($user->families()->where('family_id', $invoice->family_id)->doesntExist()) {
            return false;
        }

        // Family Editors and Admins can share invoices
        return $this->familyRoleService->isEditorOrAbove($user, $invoice->family);
    }
}
