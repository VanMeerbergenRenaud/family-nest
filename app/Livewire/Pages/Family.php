<?php

namespace App\Livewire\Pages;

use App\Enums\FamilyPermissionEnum;
use App\Enums\FamilyRelationEnum;
use App\Livewire\Forms\FamilyForm;
use App\Models\FamilyInvitation;
use App\Models\User;
use App\Traits\HumanDateTrait;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

#[Title('Famille')]
class Family extends Component
{
    use HumanDateTrait, WithPagination;

    public FamilyForm $form;

    // Sort status
    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    // Modal states
    public bool $showAddMemberModal = false;

    public bool $showCreateFamilyModal = false;

    public bool $showFamilyExempleModal = false;

    public bool $showDeleteFamilyModal = false;

    public bool $showUserProfilInfos = false;

    public bool $showDeleteMembersModal = false;

    public bool $showModifyFamilyNameModal = false;

    // Selected user and associated data
    public ?User $selectedUser = null;

    public array $selectedUserInvoiceCounts = [];

    public array $invoiceCountsCache = [];

    public function mount()
    {
        $family = auth()->user()->family();
        $this->form->setFamily($family);
    }

    public function sortBy(string $field): void
    {
        $this->sortDirection = ($this->sortField === $field && $this->sortDirection === 'asc') ? 'desc' : 'asc';
        $this->sortField = $field;
    }

    public function resetSort(): void
    {
        $this->sortField = 'name';
        $this->sortDirection = 'asc';
    }

    public function showFamilyExemple(): void
    {
        $this->showFamilyExempleModal = true;
    }

    public function openCreateFamilyModal(): void
    {
        $this->form->familyName = '';
        $this->showCreateFamilyModal = true;
    }

    public function createFamily(): void
    {
        $this->form->create();
        $this->showCreateFamilyModal = false;
    }

    public function addMember(): void
    {
        if (! $this->form->family || ! $this->form->canEdit) {
            return;
        }

        $this->form->memberPermission = FamilyPermissionEnum::Viewer->value;
        $this->form->memberRelation = FamilyRelationEnum::Member->value;
        $this->form->memberEmail = '';
        $this->showAddMemberModal = true;
    }

    public function sendInvitation(): void
    {
        if ($this->form->sendInvitation()) {
            $this->showAddMemberModal = false;
        }
    }

    public function deleteMember(int $id): void
    {
        $this->form->deleteMember($id);
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

    public function showUserProfile(int $userId): void
    {
        try {
            if (! $this->form->family) {
                return;
            }

            $this->selectedUser = User::query()
                ->join('family_user', 'users.id', '=', 'family_user.user_id')
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
                    'invoices as late_invoices' => fn ($query) => $query->where('payment_status', 'late'),
                    'invoices as unpaid_invoices' => fn ($query) => $query->where('payment_status', 'unpaid'),
                    'invoices as pending_invoices' => fn ($query) => $query->where('payment_status', 'pending'),
                ])
                ->first();

            if (! $this->selectedUser) {
                return;
            }

            $this->selectedUserInvoiceCounts = [
                'total' => $this->selectedUser->total_invoices,
                'late' => $this->selectedUser->late_invoices,
                'unpaid' => $this->selectedUser->unpaid_invoices,
                'pending' => $this->selectedUser->pending_invoices,
            ];

            $this->showUserProfilInfos = true;
        } catch (\Exception $e) {
            Toaster::error('Récupération des informations échouée::Vérifiez que vous êtes membre de la famille.');
            \Log::error('Error retrieving user profile', ['error' => $e->getMessage(), 'userId' => $userId]);
        }
    }

    public function render()
    {
        $members = collect();
        $pendingInvitations = collect();

        if ($this->form->family) {
            $members = User::query()
                ->join('family_user', 'users.id', '=', 'family_user.user_id')
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
                    'invoices as late_invoices' => fn ($query) => $query->where('payment_status', 'late'),
                    'invoices as unpaid_invoices' => fn ($query) => $query->where('payment_status', 'unpaid'),
                    'invoices as pending_invoices' => fn ($query) => $query->where('payment_status', 'pending'),
                ])
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(6);

            $this->invoiceCountsCache = [];

            foreach ($members as $member) {
                $this->invoiceCountsCache[$member->id] = [
                    'total' => $member->total_invoices,
                    'late' => $member->late_invoices,
                    'unpaid' => $member->unpaid_invoices,
                    'pending' => $member->pending_invoices,
                ];
            }

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
