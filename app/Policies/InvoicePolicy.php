<?php

namespace App\Policies;

use App\Enums\FamilyPermissionEnum;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->families()->exists();
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if (! $invoice->family_id) {
            return $user->id === $invoice->user_id;
        }

        return $user->families()
            ->where('family_id', $invoice->family_id)
            ->exists();
    }

    public function create(User $user): bool
    {
        $family = $user->family();

        if (! $family) {
            return false;
        }

        return $user->families()
            ->where('family_id', $family->id)
            ->whereIn('permission', [
                FamilyPermissionEnum::Admin->value,
                FamilyPermissionEnum::Editor->value,
            ])
            ->exists();
    }

    public function update(User $user, Invoice $invoice): bool
    {
        if (! $invoice->family_id) {
            return $user->id === $invoice->user_id;
        }

        return $user->families()
            ->where('family_id', $invoice->family_id)
            ->whereIn('permission', [
                FamilyPermissionEnum::Admin->value,
                FamilyPermissionEnum::Editor->value,
            ])
            ->exists();
    }

    public function archive(User $user, Invoice $invoice): bool
    {
        if (! $invoice->family_id) {
            return $user->id === $invoice->user_id;
        }

        return $user->families()
            ->where('family_id', $invoice->family_id)
            ->where('permission', FamilyPermissionEnum::Admin->value)
            ->exists();
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        if (! $invoice->family_id) {
            return $user->id === $invoice->user_id;
        }

        return $user->families()
            ->where('family_id', $invoice->family_id)
            ->where('permission', FamilyPermissionEnum::Admin->value)
            ->exists();
    }

    public function share(User $user, Invoice $invoice): bool
    {
        if (! $invoice->family_id) {
            return $user->id === $invoice->user_id;
        }

        return $user->families()
            ->where('family_id', $invoice->family_id)
            ->whereIn('permission', [
                FamilyPermissionEnum::Admin->value,
                FamilyPermissionEnum::Editor->value,
            ])
            ->exists();
    }
}
