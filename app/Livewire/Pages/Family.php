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

    // Propriétés pour l'invitation multiple
    public array $newMembers = [];

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

    // Méthode pour trier les membres
    public bool $isAdmin = false;

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

    // Afficher le modal d'invitation
    public function addMember(): void
    {
        $this->reset(['memberEmail', 'memberPermission', 'memberRelation', 'newMembers']);
        $this->showAddMemberModal = true;
    }

    // Ajouter un membre à la liste
    public function addMemberToList(): void
    {
        $this->validate([
            'memberEmail' => 'required|email|not_in:'.implode(',', array_column($this->newMembers, 'email')),
            'memberPermission' => 'required|in:'.implode(',', array_keys($this->availablePermissions)),
            'memberRelation' => 'required|in:'.implode(',', array_keys($this->availableRelations)),
        ], [
            'memberEmail.not_in' => 'Cette adresse email est déjà dans la liste',
        ]);

        // Vérifier si l'utilisateur est déjà membre de la famille
        $family = auth()->user()->family();
        if ($family) {
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
        }

        // Ajouter à la liste des nouveaux membres
        $this->newMembers[] = [
            'email' => $this->memberEmail,
            'permission' => $this->memberPermission,
            'relation' => $this->memberRelation,
        ];

        $this->reset(['memberEmail']);
    }

    // Supprimer un membre de la liste
    public function removeMemberFromList($index): void
    {
        if (isset($this->newMembers[$index])) {
            unset($this->newMembers[$index]);
            $this->newMembers = array_values($this->newMembers); // Réindexer le tableau
        }
    }

    // Envoyer les invitations
    public function sendInvitations(): void
    {
        $family = auth()->user()->family();

        if (! $family) {
            $this->addError('general', 'Vous devez d\'abord créer une famille');

            return;
        }

        if (empty($this->newMembers)) {
            $this->addError('general', 'Veuillez ajouter au moins un membre.');

            return;
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($this->newMembers as $member) {
            try {
                $token = Str::uuid();
                $isAdmin = $member['permission'] === 'admin';

                // Créer ou mettre à jour l'invitation
                $invitation = FamilyInvitation::updateOrCreate(
                    ['family_id' => $family->id, 'email' => $member['email']],
                    [
                        'invited_by' => auth()->id(),
                        'token' => $token,
                        'permission' => $member['permission'],
                        'relation' => $member['relation'],
                        'is_admin' => $isAdmin,
                        'expires_at' => now()->addDays(5),
                    ]
                );

                // Envoyer l'email d'invitation
                Mail::to($member['email'])->send(new FamilyInvitationMail(
                    $family,
                    auth()->user(),
                    $token,
                    $member['permission'],
                    $member['relation']
                ));

                Log::info("Invitation envoyée à {$member['email']}");

                sleep(2);

                $successCount++;
            } catch (\Exception $e) {
                Log::error("Erreur d'envoi d'invitation: ".$e->getMessage());
                $errorCount++;
            }
        }

        // Réinitialiser et fermer le modal
        $this->reset(['newMembers', 'memberEmail', 'memberPermission', 'memberRelation']);
        $this->showAddMemberModal = false;

        // Message de confirmation
        if ($successCount > 0 && $errorCount == 0) {
            session()->flash('message', "$successCount invitation(s) envoyée(s) avec succès");
        } elseif ($successCount > 0 && $errorCount > 0) {
            session()->flash('message', "$successCount invitation(s) envoyée(s) avec succès, $errorCount échec(s)");
        } else {
            session()->flash('error', "Échec de l'envoi des invitations");
        }
    }

    // Créer une nouvelle famille
    public function createFamily(): void
    {
        $this->validate([
            'familyName' => 'required|string|max:255',
        ]);

        $family = FamilyModel::create([
            'name' => $this->familyName,
        ]);

        $family->users()->attach(auth()->id(), [
            'permission' => 'admin',
            'relation' => 'self',
            'is_admin' => true,
        ]);

        session()->flash('message', 'Famille créée avec succès');
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
            $this->isAdmin = $members->contains(function ($member) {
                return $member->pivot->permission === 'admin' && $member->id === auth()->id();
            });
        }

        return view('livewire.pages.family', [
            'members' => $members,
            'family' => $family,
            'currentUser' => auth()->id(),
        ])->layout('layouts.app-sidebar');
    }
}
