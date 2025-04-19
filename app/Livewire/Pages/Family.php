<?php

namespace App\Livewire\Pages;

use App\Enums\FamilyPermissionEnum;
use App\Enums\FamilyRelationEnum;
use App\Jobs\SendFamilyInvitation;
use App\Models\Family as FamilyModel;
use App\Models\FamilyInvitation;
use App\Models\User;
use App\Traits\HumanDateTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

#[Title('Famille')]
class Family extends Component
{
    use HumanDateTrait, WithPagination;

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public ?FamilyModel $family = null;

    public array $invoiceCountsCache = [];

    // États modaux
    public bool $showAddMemberModal = false;

    public bool $showCreateFamilyModal = false;

    public bool $showFamilyExempleModal = false;

    public bool $showDeleteFamilyModal = false;

    public bool $showUserProfilInfos = false;

    public bool $showDeleteMembersModal = false;

    public bool $showModifyFamilyNameModal = false;

    // Permissions
    public bool $isAdmin = false;

    public bool $canEdit = false;

    // Formulaires
    #[Validate]
    public string $familyName = '';

    #[Validate]
    public string $memberEmail = '';

    #[Validate]
    public string $memberPermission = 'viewer';

    #[Validate]
    public string $memberRelation = 'member';

    #[Validate]
    public string $newFamilyName = '';

    // Utilisateur sélectionné
    public $selectedUser = null;

    public array $selectedUserInvoiceCounts = [];

    public function rules(): array
    {
        return [
            'familyName' => 'required|min:2|max:255',
            'memberEmail' => 'required|email',
            'memberPermission' => 'required|in:'.implode(',', array_map(fn ($case) => $case->value, FamilyPermissionEnum::cases())),
            'memberRelation' => 'required|in:'.implode(',', array_map(fn ($case) => $case->value, FamilyRelationEnum::cases())),
            'newFamilyName' => 'required|min:2|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'familyName.required' => 'Le nom de la famille est requis.',
            'memberEmail.required' => 'L\'email est requis.',
            'memberPermission.required' => 'La permission est requise.',
            'memberRelation.required' => 'La relation est requise.',
            'newFamilyName.required' => 'Le nouveau nom de la famille est requis.',
        ];
    }

    public function mount()
    {
        // Charger la famille avec la relation user pour avoir les permissions en une seule requête
        $this->family = auth()->user()->family();

        if ($this->family) {
            // Simplifier la logique de vérification des permissions avec les enums
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

    public function showFamilyExemple(): void
    {
        $this->showFamilyExempleModal = true;
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetSort(): void
    {
        $this->sortField = 'name';
        $this->sortDirection = 'asc';
    }

    public function addMember(): void
    {
        if (! $this->family || ! $this->canEdit) {
            Toaster::error('Vous n\'avez pas les permissions nécessaires pour ajouter un membre.');

            return;
        }

        // Initialiser avec les valeurs par défaut des enums
        $this->memberPermission = FamilyPermissionEnum::Viewer->value;
        $this->memberRelation = FamilyRelationEnum::Member->value;
        $this->memberEmail = '';
        $this->showAddMemberModal = true;
    }

    // Delete a family member
    public function deleteMember($id): void
    {
        if (! $this->family) {
            Toaster::error('Aucune famille trouvée.');

            return;
        }

        if (! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour supprimer le membre.');

            return;
        }

        try {
            DB::table('family_user')
                ->where('family_id', $this->family->id)
                ->where('user_id', $id)
                ->delete();

            Toaster::success('Membre supprimé avec succès');
        } catch (\Exception $e) {
            Toaster::error('Erreur lors de la suppression du membre');
            Log::error('Erreur suppression membre: '.$e->getMessage());
        }
    }

    public function openCreateFamilyModal(): void
    {
        $this->familyName = '';
        $this->showCreateFamilyModal = true;
    }

    public function sendInvitation(): void
    {
        $this->validate([
            'memberEmail' => 'required|email',
            'memberPermission' => 'required|in:'.implode(',', array_map(fn ($case) => $case->value, FamilyPermissionEnum::cases())),
            'memberRelation' => 'required|in:'.implode(',', array_map(fn ($case) => $case->value, FamilyRelationEnum::cases())),
        ]);

        if (! $this->family) {
            return;
        }

        // Vérifier les permissions
        if (! $this->canEdit) {
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
                $this->addError('memberEmail', 'Cet utilisateur est déjà membre de la famille');

                return;
            }
        }

        // Vérifier si une invitation a déjà été envoyée à l'email
        $invitationExists = FamilyInvitation::where('family_id', $this->family->id)
            ->where('email', $this->memberEmail)
            ->exists();

        if ($invitationExists) {
            $this->addError('memberEmail', 'Une invitation a déjà été envoyée à cette adresse email');

            return;
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
            $this->showAddMemberModal = false;

            Toaster::success('Invitation envoyée avec succès');
        } catch (\Exception $e) {
            Toaster::error("Erreur lors de l'envoi d'invitation::Vérifiez correctement cette adresse email et réessayez.");
            Log::error("Erreur d'envoi d'invitation: ".$e->getMessage());
        }
    }

    public function createFamily(): void
    {
        $this->validate([
            'familyName' => 'required|min:2|max:255',
        ]);

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
            $this->showCreateFamilyModal = false;

            // Mettre à jour les permissions du composant
            $this->isAdmin = true;
            $this->canEdit = true;
            $this->family = $family;

            Toaster::success('Famille créée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            Toaster::error('Erreur lors de la création de la famille::Vérifiez que le nom de la famille et réessayez.');
            Log::error('Erreur de création de famille: '.$e->getMessage());
        }
    }

    public function showDeleteFamilyMFormModal(): void
    {
        if (! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour supprimer les membres.');

            return;
        }

        $this->showDeleteMembersModal = true;
    }

    // Supprimer les membres de la famille (sans supprimer la famille elle-même)
    public function deleteMembers(): void
    {
        if (! $this->family) {
            Toaster::error('Aucune famille trouvée.');

            return;
        }

        if (! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour supprimer les membres.');

            return;
        }

        try {
            // Détacher tous les membres sauf l'utilisateur actuel en une seule requête
            DB::table('family_user')
                ->where('family_id', $this->family->id)
                ->where('user_id', '!=', auth()->id())
                ->delete();

            $this->showDeleteMembersModal = false;
            Toaster::success('Membres supprimés avec succès');
        } catch (\Exception $e) {
            Toaster::error('Erreur lors de la suppression des membres');
            Log::error('Erreur suppression membres: '.$e->getMessage());
        }
    }

    public function deleteFamily(): void
    {
        if (! $this->family) {
            Toaster::error('Aucune famille trouvée.');

            return;
        }

        if (! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour supprimer la famille.');

            return;
        }

        try {
            $this->family->delete();
            $this->showDeleteFamilyModal = false;

            // Réinitialiser les permissions
            $this->isAdmin = false;
            $this->canEdit = false;
            $this->family = null;

            Toaster::success('Famille supprimée avec succès');
        } catch (\Exception $e) {
            Toaster::error('Erreur lors de la suppression de la famille');
            Log::error('Erreur suppression famille: '.$e->getMessage());
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
            Toaster::error('Erreur lors de la modification du rôle');
            Log::error('Erreur modification rôle: '.$e->getMessage());
        }
    }

    // Renvoyer une invitation
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
            Toaster::error('Erreur lors du renvoi de l\'invitation');
            Log::error('Erreur renvoi invitation: '.$e->getMessage());
        }
    }

    // Supprimer l'invitation
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
            Toaster::error('Erreur lors de la suppression de l\'invitation');
            Log::error('Erreur suppression invitation: '.$e->getMessage());
        }
    }

    // Afficher le profil d'un utilisateur avec préchargement des données de factures
    public function showUserProfile(int $userId): void
    {
        try {
            if (! $this->family) {
                Toaster::error('Aucune famille trouvée.');

                return;
            }

            // Récupérer l'utilisateur avec Eloquent pour avoir accès à l'accesseur avatar_url
            $this->selectedUser = User::join('family_user', 'users.id', '=', 'family_user.user_id')
                ->where('family_user.family_id', $this->family->id)
                ->where('users.id', $userId)
                ->select(
                    'users.*',
                    'family_user.permission',
                    'family_user.relation',
                    'family_user.is_admin',
                    'family_user.created_at as pivot_created_at'
                )
                ->withCount([
                    'invoices as total_invoices',
                    'invoices as late_invoices' => function ($query) {
                        $query->where('payment_status', 'late');
                    },
                    'invoices as unpaid_invoices' => function ($query) {
                        $query->where('payment_status', 'unpaid');
                    },
                    'invoices as pending_invoices' => function ($query) {
                        $query->where('payment_status', 'pending');
                    },
                ])
                ->first();

            if (! $this->selectedUser) {
                Toaster::error('Utilisateur non trouvé dans votre famille.');

                return;
            }

            // Stocker les compteurs
            $this->selectedUserInvoiceCounts = [
                'total' => $this->selectedUser->total_invoices,
                'late' => $this->selectedUser->late_invoices,
                'unpaid' => $this->selectedUser->unpaid_invoices,
                'pending' => $this->selectedUser->pending_invoices,
            ];

            $this->showUserProfilInfos = true;
        } catch (\Exception $e) {
            Toaster::error('Erreur lors de l\'affichage du profil');
            Log::error('Erreur lors de l\'affichage du profil: '.$e->getMessage());
        }
    }

    // Méthode pour afficher le formulaire de modification du nom de famille
    public function showModifyFamilyNameFormModal(): void
    {
        if (! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour modifier le nom de la famille.');

            return;
        }

        if ($this->family) {
            $this->newFamilyName = $this->family->name;
            $this->showModifyFamilyNameModal = true;
        }
    }

    // Méthode pour modifier le nom de la famille
    public function updateFamilyName(): void
    {
        $this->validate([
            'newFamilyName' => 'required|min:2|max:255',
        ]);

        if (! $this->family) {
            Toaster::error('Aucune famille trouvée.');

            return;
        }

        if (! $this->isAdmin) {
            Toaster::error('Vous n\'avez pas les permissions pour modifier le nom de la famille.');

            return;
        }

        try {
            // Optimisation: utiliser une requête directe au lieu de charger et sauvegarder le modèle
            DB::table('families')
                ->where('id', $this->family->id)
                ->update(['name' => $this->newFamilyName]);

            // Mettre à jour le cache local
            $this->family->name = $this->newFamilyName;

            $this->showModifyFamilyNameModal = false;
            Toaster::success('Nom de famille modifié avec succès');
        } catch (\Exception $e) {
            Toaster::error('Erreur lors de la modification du nom de famille');
            Log::error('Erreur modification nom famille: '.$e->getMessage());
        }
    }

    public function render()
    {
        $members = collect();
        $pendingInvitations = collect();

        if ($this->family) {
            // Utiliser Eloquent au lieu de DB::table
            $members = User::join('family_user', 'users.id', '=', 'family_user.user_id')
                ->where('family_user.family_id', $this->family->id)
                ->select(
                    'users.*',
                    'family_user.permission',
                    'family_user.relation',
                    'family_user.is_admin',
                    'family_user.created_at as pivot_created_at',
                    'family_user.updated_at as pivot_updated_at'
                )
                ->withCount([
                    'invoices as total_invoices',
                    'invoices as late_invoices' => function ($query) {
                        $query->where('payment_status', 'late');
                    },
                    'invoices as unpaid_invoices' => function ($query) {
                        $query->where('payment_status', 'unpaid');
                    },
                    'invoices as pending_invoices' => function ($query) {
                        $query->where('payment_status', 'pending');
                    },
                ])
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(6);

            // Préparer les compteurs pour la vue
            $this->invoiceCountsCache = [];
            foreach ($members as $member) {
                $this->invoiceCountsCache[$member->id] = [
                    'total' => $member->total_invoices,
                    'late' => $member->late_invoices,
                    'unpaid' => $member->unpaid_invoices,
                    'pending' => $member->pending_invoices,
                ];
            }

            // Récupérer les invitations en attente
            $pendingInvitations = FamilyInvitation::where('family_id', $this->family->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('livewire.pages.family', [
            'members' => $members,
            'family' => $this->family,
            'currentUser' => auth()->id(),
            'pendingInvitations' => $pendingInvitations,
            'invoiceCounts' => $this->invoiceCountsCache,
        ])->layout('layouts.app-sidebar');
    }
}
