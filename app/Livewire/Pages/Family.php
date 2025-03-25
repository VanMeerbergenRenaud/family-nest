<?php

namespace App\Livewire\Pages;

use App\Mail\FamilyInvitation as FamilyInvitationMail;
use App\Models\Family as FamilyModel;
use App\Models\FamilyInvitation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Family extends Component
{
    use WithPagination;

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public bool $showAddMemberModal = false;

    public bool $showCreateFamilyModal = false;

    public bool $isAdmin = false;

    // Propriété pour le nom de la famille
    public string $familyName = '';

    // Propriétés pour l'invitation
    public string $memberEmail = '';

    public string $memberPermission = 'viewer';

    public string $memberRelation = 'member';

    // Options pour les rôles et relations
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
        $this->reset(['memberEmail', 'memberPermission', 'memberRelation']);
        $this->showAddMemberModal = true;
    }

    public function openCreateFamilyModal(): void
    {
        $this->reset(['familyName']);
        $this->showCreateFamilyModal = true;
    }

    public function sendInvitation(): void
    {
        $this->validate([
            'memberEmail' => 'required|email',
            'memberPermission' => 'required|in:'.implode(',', array_keys($this->availablePermissions)),
            'memberRelation' => 'required|in:'.implode(',', array_keys($this->availableRelations)),
        ]);

        $family = auth()->user()->family();

        if (! $family) {
            return;
        }

        // Vérifier si l'utilisateur est déjà membre de la famille
        $memberExists = $family->users()
            ->where('email', $this->memberEmail)
            ->exists();

        if ($memberExists) {
            $this->addError('memberEmail', 'Cet utilisateur est déjà membre de la famille');

            return;
        }

        // Vérifier si une invitation existe déjà pour cet email
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

            // Créer l'invitation
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

            // Envoyer l'email d'invitation
            Mail::to($this->memberEmail)->send(new FamilyInvitationMail(
                $family,
                auth()->user(),
                $token,
                $this->memberPermission,
                $this->memberRelation
            ));

            // Réinitialiser et fermer le modal
            $this->reset(['memberEmail', 'memberPermission', 'memberRelation']);
            $this->showAddMemberModal = false;

            session()->flash('message', 'Invitation envoyée avec succès');

        } catch (\Exception $e) {
            Log::error("Erreur d'envoi d'invitation: ".$e->getMessage());
            session()->flash('error', "Erreur lors de l'envoi de l'invitation. Veuillez réessayer.");
        }
    }

    public function createFamily(): void
    {
        $this->validate([
            'familyName' => 'required|string|max:255',
        ]);

        try {
            // Créer la famille
            $family = FamilyModel::create([
                'name' => $this->familyName,
            ]);

            // Attacher l'utilisateur actuel comme admin
            $family->users()->attach(auth()->id(), [
                'permission' => 'admin',
                'relation' => 'self',
                'is_admin' => true,
            ]);

            // Réinitialiser et fermer le modal
            $this->reset(['familyName']);
            $this->showCreateFamilyModal = false;

            session()->flash('message', 'Famille créée avec succès');

            // Recharger la page pour afficher la nouvelle famille
            $this->redirectRoute('family');

        } catch (\Exception $e) {
            Log::error('Erreur de création de famille: '.$e->getMessage());
            session()->flash('error', 'Erreur lors de la création de la famille. Veuillez réessayer.');
        }
    }

    public function render()
    {
        $family = auth()->user()->family();

        if (! $family) {
            $members = collect();
            $this->isAdmin = false;
        } else {
            // Récupération des membres de la famille
            $members = $family->users()
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(6);

            // Déterminer si l'utilisateur actuel est administrateur
            $this->isAdmin = auth()->user()->isAdminOfFamily();
        }

        return view('livewire.pages.family', [
            'members' => $members,
            'family' => $family,
            'currentUser' => auth()->id(),
        ])->layout('layouts.app-sidebar');
    }
}
