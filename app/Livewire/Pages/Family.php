<?php

namespace App\Livewire\Pages;

use App\Mail\FamilyInvitation as FamilyInvitationMail;
use App\Models\Family as FamilyModel;
use App\Models\FamilyInvitation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

#[Title('Famille')]
class Family extends Component
{
    use WithPagination;

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public bool $showAddMemberModal = false;

    public bool $showCreateFamilyModal = false;

    public bool $isAdmin = false;

    #[Validate('required|min:2|max:255')]
    public string $familyName = '';

    #[Validate('required|email')]
    public string $memberEmail = '';

    #[Validate('required|in:admin,editor,viewer')]
    public string $memberPermission = 'viewer';

    #[Validate('required|in:member,spouse,parent,child,sibling,friend,other')]
    public string $memberRelation = 'member';

    public array $availablePermissions = [
        'admin' => 'Administrateur',
        'editor' => 'Éditeur',
        'viewer' => 'Lecteur',
    ];

    public array $availableRelations = [
        'member' => 'Membre',
        'spouse' => 'Conjoint(e)',
        'parent' => 'Parent',
        'child' => 'Enfant',
        'sibling' => 'Frère/Sœur',
        'friend' => 'Ami(e)',
        'other' => 'Autre',
    ];

    public bool $showFamilyExempleModal;

    public bool $showDeleteFamilyModal;

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
        $this->showAddMemberModal = true;
    }

    public function openCreateFamilyModal(): void
    {
        $this->showCreateFamilyModal = true;
    }

    public function sendInvitation(): void
    {
        $this->validate([
            'memberEmail' => 'required|email',
            'memberPermission' => 'required|in:admin,editor,viewer',
            'memberRelation' => 'required|in:member,spouse,parent,child,sibling,friend,other',
        ]);

        $family = auth()->user()->family();

        if (! $family) {
            return;
        }

        // Verify if the user is already a member of the family
        $memberExists = $family->users()
            ->where('email', $this->memberEmail)
            ->exists();

        if ($memberExists) {
            $this->addError('memberEmail', 'Cet utilisateur est déjà membre de la famille');

            return;
        }

        // Verify if an invitation has already been sent to the email
        $invitationExists = FamilyInvitation::where('family_id', $family->id)
            ->where('email', $this->memberEmail)
            ->exists();

        if ($invitationExists) {
            $this->addError('memberEmail', 'Une invitation a déjà été envoyée à cette adresse email');

            return;
        }

        try {
            $token = Str::uuid();
            $isAdmin = $this->memberPermission === 'admin';

            // Create the invitation
            $invitation = FamilyInvitation::create([
                'family_id' => $family->id,
                'invited_by' => auth()->id(),
                'email' => $this->memberEmail,
                'token' => $token,
                'permission' => $this->memberPermission,
                'relation' => $this->memberRelation,
                'is_admin' => $isAdmin,
                'expires_at' => now()->addDays(7),
            ]);

            // Send the invitation email
            Mail::to($this->memberEmail)->send(new FamilyInvitationMail(
                $family,
                auth()->user(),
                $token,
                $this->memberPermission,
                $this->memberRelation
            ));

            $this->reset(['memberEmail', 'memberPermission', 'memberRelation']);
            $this->showAddMemberModal = false;

            Toaster::success('Invitation envoyée avec succès');

        } catch (\Exception $e) {
            Log::error("Erreur d'envoi d'invitation: ".$e->getMessage());
            Toaster::error("Erreur lors de l'envoi d'invitation::Vérifiez correctement cette adresse email et réessayez.");
        }
    }

    public function createFamily(): void
    {
        $this->validate([
            'familyName' => 'required|min:2|max:255',
        ]);

        try {
            $family = FamilyModel::create([
                'name' => $this->familyName,
            ]);

            // Attach the current user to the family
            $family->users()->attach(auth()->id(), [
                'permission' => 'admin',
                'relation' => 'self',
                'is_admin' => true,
            ]);

            $this->reset(['familyName']);
            $this->showCreateFamilyModal = false;

            Toaster::success('Famille créée avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur de création de famille: '.$e->getMessage());
            Toaster::error('Erreur lors de la création de la famille::Vérifiez que le nom de la famille et réessayez.');
        }
    }

    public function showDeleteFamilyMFormModal(): void
    {
        $this->showDeleteFamilyModal = true;
    }

    public function deleteFamily(): void
    {
        $family = auth()->user()->family();

        if ($family) {
            $family->delete();
            $this->showDeleteFamilyModal = false;
            Toaster::success('Famille supprimée avec succès');
        } else {
            Toaster::error('Erreur lors de la suppression de la famille');
        }
    }

    public function render()
    {
        $family = auth()->user()->family();

        if (! $family) {
            $members = collect();
            $this->isAdmin = false;
        } else {
            $members = $family->users()
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(6);

            // Verify if the user is the admin of the family
            $this->isAdmin = auth()->user()->isAdminOfFamily();
        }

        return view('livewire.pages.family', [
            'members' => $members,
            'family' => $family,
            'currentUser' => auth()->id(),
        ])->layout('layouts.app-sidebar');
    }
}
