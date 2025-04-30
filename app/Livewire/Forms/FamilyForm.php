<?php

namespace App\Livewire\Forms;

use App\Enums\FamilyPermissionEnum;
use App\Enums\FamilyRelationEnum;
use App\Jobs\SendFamilyInvitation;
use App\Models\Family;
use App\Models\FamilyInvitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Masmerise\Toaster\Toaster;

class FamilyForm extends Form
{
    public ?Family $family = null;

    #[Validate]
    public string $familyName = '';

    #[Validate]
    public string $newFamilyName = '';

    #[Validate]
    public string $memberEmail = '';

    #[Validate]
    public string $memberPermission = 'viewer';

    #[Validate]
    public string $memberRelation = 'other';

    public bool $isAdmin = false;

    public bool $canEdit = false;

    public function rules(): array
    {
        return [
            'familyName' => 'required|min:2|max:255',
            'newFamilyName' => 'required|min:2|max:255',
            'memberEmail' => 'required|email',
            'memberPermission' => 'required|in:'.implode(',', array_map(fn ($case) => $case->value, FamilyPermissionEnum::cases())),
            'memberRelation' => 'required|in:'.implode(',', array_map(fn ($case) => $case->value, FamilyRelationEnum::cases())),
        ];
    }

    public function messages(): array
    {
        return [
            'familyName.required' => 'Le nom de la famille est requis.',
            'newFamilyName.required' => 'Le nom de votre nouvelle famille est requis.',
            'memberEmail.required' => 'L\'email du membre est requis.',
            'memberPermission.required' => 'Le rôle du membre est requise.',
            'memberRelation.required' => 'La relation du membre est requise.',
        ];
    }

    public function setFamily(?Family $family): void
    {
        $this->family = $family;

        if (! $this->family) {
            $this->isAdmin = $this->canEdit = false;

            return;
        }

        $permission = FamilyPermissionEnum::tryFrom(
            $this->family->users()
                ->where('users.id', Auth::id())
                ->first()
                ?->pivot->permission ?? 'viewer'
        );

        if ($permission) {
            $this->isAdmin = $permission->isAdmin();
            $this->canEdit = $permission->canEdit();
        }
    }

    public function prepareForModification(): void
    {
        if ($this->family) {
            $this->newFamilyName = $this->family->name;
        }
    }

    public function create(): ?Family
    {
        $this->validate(['familyName' => 'required|min:2|max:255']);

        try {
            DB::beginTransaction();

            $family = Family::create(['name' => $this->familyName]);

            $family->users()->attach(Auth::id(), [
                'permission' => FamilyPermissionEnum::Admin->value,
                'relation' => FamilyRelationEnum::Self->value,
                'is_admin' => true,
            ]);

            DB::commit();

            $this->reset('familyName');
            $this->setFamily($family);

            // Si le user possède déjà des factures, les associer à la famille
            if (auth()->user()->invoices()->exists()) {
                $userInvoices = DB::table('invoices')
                    ->where('user_id', auth()->id())
                    ->where('family_id', null)
                    ->get();

                foreach ($userInvoices as $invoice) {
                    DB::table('invoices')
                        ->where('id', $invoice->id)
                        ->update([
                            'family_id' => $family->id,
                            'paid_by_user_id' => auth()->id(),
                        ]);
                }

                Toaster::success('Anciennes factures associées à la famille');
            }

            Toaster::success('Famille créée avec succès');

            return $family;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur de création de famille: '.$e->getMessage());
            Toaster::error('Erreur lors de la création de la famille::Vérifiez que le nom de la famille et réessayez.');

            return null;
        }
    }

    public function updateName(): bool
    {
        $this->validate(['newFamilyName' => 'required|min:2|max:255']);

        if (! $this->family || ! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour modifier le nom de la famille.');

            return false;
        }

        try {
            $this->family->update(['name' => $this->newFamilyName]);
            $this->reset('newFamilyName');

            Toaster::success('Nom de famille modifié avec succès');

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur modification nom famille: '.$e->getMessage());
            Toaster::error('Erreur lors de la modification du nom de famille');

            return false;
        }
    }

    public function delete(): bool
    {
        if (! $this->family || ! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour supprimer la famille.');

            return false;
        }

        try {
            $this->family->delete();
            $this->setFamily(null);

            Toaster::success('Famille supprimée avec succès');

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur suppression famille: '.$e->getMessage());
            Toaster::error('Erreur lors de la suppression de la famille');

            return false;
        }
    }

    public function sendInvitation(): bool
    {
        $this->validate([
            'memberEmail' => 'required|email',
            'memberPermission' => 'required|in:'.implode(',', array_map(fn ($case) => $case->value, FamilyPermissionEnum::cases())),
            'memberRelation' => 'required|in:'.implode(',', array_map(fn ($case) => $case->value, FamilyRelationEnum::cases())),
        ]);

        if (! $this->family || ! $this->canEdit) {
            Toaster::error('Vous n\'avez pas les permissions pour inviter un membre.');

            return false;
        }

        if (! $this->isAdmin && $this->memberPermission === FamilyPermissionEnum::Admin->value) {
            Toaster::error('Seul un administrateur peut attribuer le rôle d\'administrateur.');

            return false;
        }

        $memberExists = DB::table('family_user')
            ->where('family_id', $this->family->id)
            ->join('users', 'family_user.user_id', '=', 'users.id')
            ->where('users.email', $this->memberEmail)
            ->exists();

        if ($memberExists) {
            Toaster::error('Cet utilisateur est déjà membre de la famille.');

            return false;
        }

        $invitationExists = FamilyInvitation::where('family_id', $this->family->id)
            ->where('email', $this->memberEmail)
            ->exists();

        if ($invitationExists) {
            Toaster::error('Une invitation a déjà été envoyée à cet email.');

            return false;
        }

        try {
            $permission = FamilyPermissionEnum::tryFrom($this->memberPermission);
            $invitation = FamilyInvitation::create([
                'family_id' => $this->family->id,
                'invited_by' => Auth::id(),
                'email' => $this->memberEmail,
                'token' => Str::uuid(),
                'permission' => $this->memberPermission,
                'relation' => $this->memberRelation,
                'is_admin' => $permission?->isAdmin() ?? false,
                'expires_at' => now()->addDays(7),
                'send_failed' => false,
            ]);

            SendFamilyInvitation::dispatch($invitation, $this->family, Auth::user());

            $this->reset(['memberEmail', 'memberPermission', 'memberRelation']);
            Toaster::success('Invitation envoyée avec succès');

            return true;
        } catch (\Exception $e) {
            Log::error("Erreur d'envoi d'invitation: ".$e->getMessage());
            Toaster::error("Erreur lors de l'envoi d'invitation::Vérifiez correctement cette adresse email et réessayez.");

            return false;
        }
    }

    public function createInvitations(array $invitationData): int
    {
        if (! $this->family || ! $this->canEdit) {
            Toaster::error('Vous n\'avez pas les permissions pour inviter des membres.');

            return 0;
        }

        $sentCount = 0;

        foreach ($invitationData as $member) {
            try {
                $permission = FamilyPermissionEnum::tryFrom($member['permission']);

                if (! $this->isAdmin && $permission === FamilyPermissionEnum::Admin) {
                    continue;
                }

                $invitation = FamilyInvitation::create([
                    'family_id' => $this->family->id,
                    'invited_by' => Auth::id(),
                    'email' => $member['email'],
                    'token' => Str::uuid(),
                    'permission' => $member['permission'],
                    'relation' => $member['relation'],
                    'is_admin' => $permission?->isAdmin() ?? false,
                    'expires_at' => now()->addDays(7),
                    'send_failed' => false,
                ]);

                SendFamilyInvitation::dispatch($invitation, $this->family, Auth::user());
                $sentCount++;
            } catch (\Exception $e) {
                Log::error("Erreur d'envoi d'invitation: ".$e->getMessage());
            }
        }

        return $sentCount;
    }

    public function changeRole(int $userId, string $newRole): bool
    {
        if (! $this->family || ! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour modifier les rôles.');

            return false;
        }

        $permission = FamilyPermissionEnum::tryFrom($newRole);
        if (! $permission) {
            Toaster::error('Rôle invalide.');

            return false;
        }

        try {
            $this->family->users()->updateExistingPivot($userId, [
                'permission' => $newRole,
                'is_admin' => $permission->isAdmin(),
            ]);

            Toaster::success('Rôle modifié avec succès');

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur modification rôle: '.$e->getMessage());
            Toaster::error('Erreur lors de la modification du rôle');

            return false;
        }
    }

    public function deleteMember(int $userId): bool
    {
        if (! $this->family || ! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour supprimer le membre.');

            return false;
        }

        try {
            $this->family->users()->detach($userId);

            // Mettre le family_id de chaque invoice à NULL
            DB::table('invoices')
                ->where('user_id', $userId)
                ->update(['family_id' => null]);

            Toaster::success('Membre supprimé avec succès');

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur suppression membre: '.$e->getMessage());
            Toaster::error('Erreur lors de la suppression du membre');

            return false;
        }
    }

    public function deleteAllMembers(): bool
    {
        if (! $this->family || ! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour supprimer les membres.');

            return false;
        }

        try {
            $this->family->users()->wherePivot('user_id', '!=', Auth::id())->detach();
            Toaster::success('Membres supprimés avec succès');

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur suppression membres: '.$e->getMessage());
            Toaster::error('Erreur lors de la suppression des membres');

            return false;
        }
    }

    public function resendInvitation(int $invitationId): bool
    {
        if (! $this->canEdit) {
            Toaster::error('Vous n\'avez pas les permissions nécessaires.');

            return false;
        }

        try {
            $invitation = FamilyInvitation::whereHas('family', function ($query) {
                $query->where('id', $this->family->id);
            })->find($invitationId);

            if (! $invitation) {
                Toaster::error('Invitation non trouvée ou n\'appartient pas à votre famille.');

                return false;
            }

            $invitation->resend();
            Toaster::success('Invitation renvoyée avec succès');

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur renvoi invitation: '.$e->getMessage());
            Toaster::error('Erreur lors du renvoi de l\'invitation');

            return false;
        }
    }

    public function deleteInvitation(int $invitationId): bool
    {
        if (! $this->canEdit) {
            Toaster::error('Vous n\'avez pas les permissions nécessaires.');

            return false;
        }

        try {
            $invitation = FamilyInvitation::whereHas('family', function ($query) {
                $query->where('id', $this->family->id);
            })->find($invitationId);

            if (! $invitation) {
                Toaster::error('Invitation non trouvée ou n\'appartient pas à votre famille.');

                return false;
            }

            $invitation->delete();
            Toaster::success('Invitation supprimée avec succès');

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur suppression invitation: '.$e->getMessage());
            Toaster::error('Erreur lors de la suppression de l\'invitation');

            return false;
        }
    }
}
