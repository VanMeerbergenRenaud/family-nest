<?php

namespace App\Livewire\Forms;

use App\Enums\FamilyPermissionEnum;
use App\Enums\FamilyRelationEnum;
use App\Jobs\SendFamilyInvitation;
use App\Models\Family as FamilyModel;
use App\Models\FamilyInvitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Masmerise\Toaster\Toaster;

class FamilyForm extends Form
{
    public ?FamilyModel $family = null;

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

    public function setFamily($family): void
    {
        $this->family = $family;

        if ($this->family) {
            $permission = FamilyPermissionEnum::tryFrom(
                $this->family->users()
                    ->where('users.id', auth()->id())
                    ->first()
                    ->pivot->permission ?? 'viewer'
            );

            if ($permission) {
                $this->isAdmin = $permission->isAdmin();
                $this->canEdit = $permission->canEdit();
            }
        }
    }

    public function prepareForModification(): void
    {
        if ($this->family) {
            $this->newFamilyName = $this->family->name;
        }
    }

    public function create(): void
    {
        $this->validate(['familyName' => 'required|min:2|max:255']);

        try {
            DB::beginTransaction();

            $family = FamilyModel::create([
                'name' => $this->familyName,
            ]);

            // Attacher l'utilisateur actuel à la famille avec le rôle Admin
            $family->users()->attach(auth()->id(), [
                'permission' => FamilyPermissionEnum::Admin->value,
                'relation' => FamilyRelationEnum::Self->value,
                'is_admin' => true,
            ]);

            DB::commit();

            $this->reset(['familyName']);

            // Mettre à jour les permissions
            $this->isAdmin = true;
            $this->canEdit = true;
            $this->family = $family;

            Toaster::success('Famille créée avec succès');

            return;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur de création de famille: '.$e->getMessage());
            Toaster::error('Erreur lors de la création de la famille::Vérifiez que le nom de la famille et réessayez.');
        }
    }

    public function updateName(): void
    {
        $this->validate(['newFamilyName' => 'required|min:2|max:255']);

        if (! $this->family || ! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour modifier le nom de la famille.');

            return;
        }

        try {
            $this->family->update(['name' => $this->newFamilyName]);
            $this->reset(['newFamilyName']);

            Toaster::success('Nom de famille modifié avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur modification nom famille: '.$e->getMessage());
            Toaster::error('Erreur lors de la modification du nom de famille');
        }
    }

    public function delete(): void
    {
        if (! $this->family || ! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour supprimer la famille.');

            return;
        }

        try {
            $this->family->delete();

            // Réinitialiser les permissions
            $this->isAdmin = false;
            $this->canEdit = false;
            $this->family = null;

            Toaster::success('Famille supprimée avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur suppression famille: '.$e->getMessage());
            Toaster::error('Erreur lors de la suppression de la famille');
        }
    }

    public function sendInvitation(): void
    {
        $this->validate([
            'memberEmail' => 'required|email',
            'memberPermission' => 'required|in:'.implode(',', array_map(fn ($case) => $case->value, FamilyPermissionEnum::cases())),
            'memberRelation' => 'required|in:'.implode(',', array_map(fn ($case) => $case->value, FamilyRelationEnum::cases())),
        ]);

        if (! $this->family || ! $this->canEdit) {
            Toaster::error('Vous n\'avez pas les permissions pour inviter un membre.');

            return;
        }

        // Si l'utilisateur est éditeur mais pas admin, il ne peut pas inviter un admin
        if (! $this->isAdmin && $this->memberPermission === FamilyPermissionEnum::Admin->value) {
            Toaster::error('Seul un administrateur peut attribuer le rôle d\'administrateur.');

            return;
        }

        // Vérifications en une seule requête
        $existingUser = DB::table('users')
            ->where('email', $this->memberEmail)
            ->exists();

        if ($existingUser) {
            $memberExists = DB::table('family_user')
                ->where('family_id', $this->family->id)
                ->join('users', 'family_user.user_id', '=', 'users.id')
                ->where('users.email', $this->memberEmail)
                ->exists();

            if ($memberExists) {
                Toaster::error('Cet utilisateur est déjà membre de la famille.');
            }
        }

        // Vérifier si une invitation a déjà été envoyée à l'email
        $invitationExists = FamilyInvitation::where('family_id', $this->family->id)
            ->where('email', $this->memberEmail)
            ->exists();

        if ($invitationExists) {
            Toaster::error('Une invitation a déjà été envoyée à cet email.');
        }

        try {
            $token = Str::uuid();
            $permission = FamilyPermissionEnum::tryFrom($this->memberPermission);
            $isAdmin = $permission && $permission->isAdmin();

            // Créer l'invitation
            $invitation = FamilyInvitation::create([
                'family_id' => $this->family->id,
                'invited_by' => auth()->id(),
                'email' => $this->memberEmail,
                'token' => $token,
                'permission' => $this->memberPermission,
                'relation' => $this->memberRelation,
                'is_admin' => $isAdmin,
                'expires_at' => now()->addDays(7),
                'send_failed' => false,
            ]);

            // Envoyer l'invitation email via un job
            SendFamilyInvitation::dispatch(
                $invitation,
                $this->family,
                auth()->user()
            );

            $this->reset(['memberEmail', 'memberPermission', 'memberRelation']);

            Toaster::success('Invitation envoyée avec succès');

            return;

        } catch (\Exception $e) {
            Log::error("Erreur d'envoi d'invitation: ".$e->getMessage());
            Toaster::error("Erreur lors de l'envoi d'invitation::Vérifiez correctement cette adresse email et réessayez.");

            return;
        }
    }

    public function changeRole(int $userId, string $newRole): void
    {
        if (! $this->family || ! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour modifier les rôles.');

            return;
        }

        // Vérifier que le nouveau rôle est valide avec l'enum
        $permission = FamilyPermissionEnum::tryFrom($newRole);
        if (! $permission) {
            Toaster::error('Rôle invalide.');

            return;
        }

        try {
            // Mettre à jour le rôle du membre
            DB::table('family_user')
                ->where('family_id', $this->family->id)
                ->where('user_id', $userId)
                ->update([
                    'permission' => $newRole,
                    'is_admin' => $permission->isAdmin(),
                ]);

            Toaster::success('Rôle modifié avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur modification rôle: '.$e->getMessage());
            Toaster::error('Erreur lors de la modification du rôle');
        }
    }

    public function deleteMember(int $userId): void
    {
        if (! $this->family || ! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour supprimer le membre.');

            return;
        }

        try {
            DB::table('family_user')
                ->where('family_id', $this->family->id)
                ->where('user_id', $userId)
                ->delete();

            Toaster::success('Membre supprimé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur suppression membre: '.$e->getMessage());
            Toaster::error('Erreur lors de la suppression du membre');
        }
    }

    public function deleteAllMembers(): void
    {
        if (! $this->family || ! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour supprimer les membres.');

            return;
        }

        try {
            // Détacher tous les membres sauf l'utilisateur actuel en une seule requête
            DB::table('family_user')
                ->where('family_id', $this->family->id)
                ->where('user_id', '!=', auth()->id())
                ->delete();

            Toaster::success('Membres supprimés avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur suppression membres: '.$e->getMessage());
            Toaster::error('Erreur lors de la suppression des membres');
        }
    }

    public function resendInvitation(int $invitationId): void
    {
        if (! $this->canEdit) {
            Toaster::error('Vous n\'avez pas les permissions nécessaires.');

            return;
        }

        try {
            // Récupérer l'invitation avec sa famille en une seule requête
            $invitation = FamilyInvitation::with('family')
                ->where('id', $invitationId)
                ->first();

            if (! $invitation) {
                Toaster::error('Invitation non trouvée.');

                return;
            }

            // Vérifier que l'invitation appartient à la famille de l'utilisateur
            if ($invitation->family->id !== $this->family->id) {
                Toaster::error('Cette invitation n\'appartient pas à votre famille.');

                return;
            }

            // Renvoyer l'invitation
            $invitation->resend();

            Toaster::success('Invitation renvoyée avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur renvoi invitation: '.$e->getMessage());
            Toaster::error('Erreur lors du renvoi de l\'invitation');
        }
    }

    public function deleteInvitation(int $invitationId): void
    {
        if (! $this->canEdit) {
            Toaster::error('Vous n\'avez pas les permissions nécessaires.');

            return;
        }

        try {
            FamilyInvitation::destroy($invitationId);

            Toaster::success('Invitation supprimée avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur suppression invitation: '.$e->getMessage());
            Toaster::error('Erreur lors de la suppression de l\'invitation');
        }
    }
}
