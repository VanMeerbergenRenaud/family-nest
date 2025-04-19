<?php

namespace App\Livewire\Pages;

use App\Enums\FamilyPermissionEnum;
use App\Enums\FamilyRelationEnum;
use App\Livewire\Forms\FamilyForm;
use App\Models\FamilyInvitation;
use App\Models\User;
use App\Traits\HumanDateTrait;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Famille')]
class Family extends Component
{
    use HumanDateTrait, WithPagination;

    // Propriété du formulaire
    public FamilyForm $form;

    // État de tri
    public $sortField = 'name';

    public $sortDirection = 'asc';

    // États modaux
    public bool $showAddMemberModal = false;

    public bool $showCreateFamilyModal = false;

    public bool $showFamilyExempleModal = false;

    public bool $showDeleteFamilyModal = false;

    public bool $showUserProfilInfos = false;

    public bool $showDeleteMembersModal = false;

    public bool $showModifyFamilyNameModal = false;

    // Utilisateur sélectionné
    public $selectedUser = null;

    public array $selectedUserInvoiceCounts = [];

    public array $invoiceCountsCache = [];

    public function mount()
    {
        // Charger la famille avec la relation user pour avoir les permissions en une seule requête
        $family = auth()->user()->family();
        $this->form->setFamily($family);
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
        if (! $this->form->family || ! $this->form->canEdit) {
            return;
        }

        // Initialiser avec les valeurs par défaut des enums
        $this->form->memberPermission = FamilyPermissionEnum::Viewer->value;
        $this->form->memberRelation = FamilyRelationEnum::Member->value;
        $this->form->memberEmail = '';
        $this->showAddMemberModal = true;
    }

    public function deleteMember($id): void
    {
        $this->form->deleteMember($id);
    }

    public function openCreateFamilyModal(): void
    {
        $this->form->familyName = '';
        $this->showCreateFamilyModal = true;
    }

    public function sendInvitation(): void
    {
        $result = $this->form->sendInvitation();

        if (isset($result['error'])) {
            $this->addError($result['error'], $result['message']);

            return;
        }

        if (isset($result['success']) && $result['success']) {
            $this->showAddMemberModal = false;
        }
    }

    public function createFamily(): void
    {
        $this->form->create();
        $this->showCreateFamilyModal = false;
    }

    public function showDeleteFamilyMFormModal(): void
    {
        if (! $this->form->isAdmin) {
            return;
        }

        $this->showDeleteMembersModal = true;
    }

    public function deleteMembers(): void
    {
        $this->form->deleteAllMembers();
        $this->showDeleteMembersModal = false;
    }

    public function deleteFamily(): void
    {
        $this->form->delete();
        $this->showDeleteFamilyModal = false;
    }

    public function changeRole(int $userId, string $newRole): void
    {
        $this->form->changeRole($userId, $newRole);
    }

    public function resendInvitation(int $invitationId): void
    {
        $this->form->resendInvitation($invitationId);
    }

    public function deleteInvitation(int $invitationId): void
    {
        $this->form->deleteInvitation($invitationId);
    }

    public function showUserProfile(int $userId): void
    {
        try {
            if (! $this->form->family) {
                return;
            }

            // Récupérer l'utilisateur avec Eloquent pour avoir accès à l'accesseur avatar_url
            $this->selectedUser = User::join('family_user', 'users.id', '=', 'family_user.user_id')
                ->where('family_user.family_id', $this->form->family->id)
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
            // Gestion d'erreur déjà faite dans le contrôleur
        }
    }

    public function showModifyFamilyNameFormModal(): void
    {
        if (! $this->form->isAdmin) {
            return;
        }

        $this->form->prepareForModification();
        $this->showModifyFamilyNameModal = true;
    }

    public function updateFamilyName(): void
    {
        $this->form->updateName();
        $this->showModifyFamilyNameModal = false;
    }

    public function render()
    {
        $members = collect();
        $pendingInvitations = collect();

        if ($this->form->family) {
            // Utiliser Eloquent au lieu de DB::table
            $members = User::join('family_user', 'users.id', '=', 'family_user.user_id')
                ->where('family_user.family_id', $this->form->family->id)
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
            $pendingInvitations = FamilyInvitation::where('family_id', $this->form->family->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('livewire.pages.family', [
            'members' => $members,
            'family' => $this->form->family,
            'currentUser' => auth()->id(),
            'pendingInvitations' => $pendingInvitations,
            'invoiceCounts' => $this->invoiceCountsCache,
            'isAdmin' => $this->form->isAdmin,
            'canEdit' => $this->form->canEdit,
        ])->layout('layouts.app-sidebar');
    }
}
