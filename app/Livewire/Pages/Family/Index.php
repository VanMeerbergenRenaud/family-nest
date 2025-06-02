<?php

namespace App\Livewire\Pages\Family;

use App\Enums\FamilyPermissionEnum;
use App\Enums\FamilyRelationEnum;
use App\Livewire\Forms\FamilyForm;
use App\Models\FamilyInvitation;
use App\Models\User;
use App\Traits\Family\SortableTrait;
use App\Traits\HumanDateTrait;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Famille')]
class Index extends Component
{
    use HumanDateTrait, SortableTrait, WithPagination;

    public FamilyForm $form;

    public int $perPage = 8;

    // Modal states
    public bool $showAddMemberModal = false;

    public bool $showCreateFamilyModal = false;

    public bool $showFamilyExempleModal = false;

    public bool $showDeleteFamilyModal = false;

    public bool $showUserProfil = false;

    public bool $showDeleteMembersModal = false;

    public bool $showModifyFamilyNameModal = false;

    // Selected user
    public ?User $selectedUser = null;

    public array $selectedUserInvoiceCounts = [];

    protected array $sortableColumns = ['name', 'permission', 'relation'];

    public function mount(): void
    {
        $this->form->setFamily(auth()->user()->family());
    }

    #[Computed]
    public function members()
    {
        if (! $this->form->family) {
            return collect();
        }

        $query = User::query()
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
                'invoices as late_invoices' => fn ($q) => $q->where('payment_status', 'late'),
                'invoices as unpaid_invoices' => fn ($q) => $q->where('payment_status', 'unpaid'),
                'invoices as pending_invoices' => fn ($q) => $q->where('payment_status', 'pending'),
            ]);

        return $this->applySorting($query)->paginate($this->perPage);
    }

    #[Computed]
    public function pendingInvitations()
    {
        return $this->form->family
            ? FamilyInvitation::where('family_id', $this->form->family->id)
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();
    }

    #[Computed]
    public function invoiceCounts()
    {
        return $this->members->mapWithKeys(fn ($member) => [
            $member->id => [
                'total' => $member->total_invoices,
                'late' => $member->late_invoices,
                'unpaid' => $member->unpaid_invoices,
                'pending' => $member->pending_invoices,
            ],
        ])->toArray();
    }

    // Modal actions
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
        if ($this->form->isAdmin) {
            $this->showDeleteMembersModal = true;
        }
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
        if ($this->form->isAdmin) {
            $this->form->prepareForModification();
            $this->showModifyFamilyNameModal = true;
        }
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
                    'invoices as late_invoices' => fn ($q) => $q->where('payment_status', 'late'),
                    'invoices as unpaid_invoices' => fn ($q) => $q->where('payment_status', 'unpaid'),
                    'invoices as pending_invoices' => fn ($q) => $q->where('payment_status', 'pending'),
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

            $this->showUserProfil = true;
        } catch (\Exception $e) {
            \Log::error('Error retrieving user profile', ['error' => $e->getMessage(), 'userId' => $userId]);
        }
    }

    public function render()
    {
        return view('livewire.pages.family.index', [
            'family' => $this->form->family,
            'currentUser' => auth()->id(),
            'isAdmin' => $this->form->isAdmin,
            'canEdit' => $this->form->canEdit,
            'familyPermissionEnum' => FamilyPermissionEnum::class,
            'familyRelationEnum' => FamilyRelationEnum::class,
            'permissionOptions' => FamilyPermissionEnum::getPermissionOptions(),
            'relationOptions' => FamilyRelationEnum::getRelationOptions(),
        ])->layout('layouts.app-sidebar');
    }
}
