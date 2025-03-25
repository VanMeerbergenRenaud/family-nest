<?php

namespace App\Livewire;

use App\Livewire\Actions\Logout;
use App\Livewire\Forms\RegisterForm;
use App\Models\FamilyInvitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class FamilyInvitationHandler extends Component
{
    public ?FamilyInvitation $invitation = null;

    public RegisterForm $form;

    public string $token = '';

    public function mount($token)
    {
        $this->token = $token;
        $this->invitation = FamilyInvitation::where('token', $token)->first();

        // Vérifier si l'invitation existe
        if (! $this->invitation || $this->invitation->isExpired()) {
            $this->redirectRoute('welcome');
        }

        // Initialiser l'email dans le formulaire
        $this->form->email = $this->invitation->email;

        // Vérifier si l'utilisateur connecté a un email différent de l'invitation
        if (Auth::check() && Auth::user()->email !== $this->invitation->email) {
            session()->flash('error', 'Vous devez vous connecter avec le compte associé à cette invitation.');
        }
    }

    public function acceptInvitation(): void
    {
        // Vérifier si l'invitation est valide
        if (! $this->validateInvitation()) {
            $this->redirectRoute('welcome', ['error' => 'Cette invitation n\'est plus valide.']);
        }

        try {
            $this->processInvitation();
            $this->redirectRoute('family');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'acceptation de l\'invitation', ['error' => $e->getMessage()]);
            session()->flash('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function register(): void
    {
        if (! $this->validateInvitation()) {
            return;
        }

        try {
            // Méthode RegisterForm pour créer le compte
            $this->form->register();

            // Lier l'utilisateur à la famille
            $this->processInvitation();

            // Redirection selon configuration
            $route = config('app.email_verification_enabled', true)
                ? 'verification.notice'
                : 'family';

            $this->redirectRoute($route);

        } catch (\Exception $e) {
            Log::error('Erreur création compte', ['error' => $e->getMessage()]);
            session()->flash('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    private function validateInvitation(): bool
    {
        // Vérification de l'invitation
        if (! $this->invitation || $this->invitation->isExpired()) {
            session()->flash('error', 'Cette invitation n\'est plus valide.');

            return false;
        }

        // Vérification de l'email
        if (Auth::check() && Auth::user()->email !== $this->invitation->email) {
            session()->flash('error', 'Vous devez vous connecter avec le compte associé à cette invitation.');

            return false;
        }

        return true;
    }

    private function processInvitation(): void
    {
        // Récupération des données d'invitation avec valeurs par défaut
        $permission = $this->invitation->permission ?? 'viewer';
        $relation = $this->invitation->relation ?? 'member';
        $isAdmin = $this->invitation->is_admin ?? false;

        // Lier l'utilisateur à la famille
        $this->invitation->family->users()->attach(Auth::id(), [
            'permission' => $permission,
            'relation' => $relation,
            'is_admin' => $isAdmin,
        ]);

        // Supprimer l'invitation
        $this->invitation->delete();
    }

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        // Invitation expirée
        if (! $this->invitation || $this->invitation->isExpired()) {
            return view('livewire.family-invitation.expired')->layout('layouts.guest');
        }

        // Utilisateur connecté avec email correspondant
        if (Auth::check() && Auth::user()->email === $this->invitation->email) {
            return view('livewire.family-invitation.accept', [
                'invitation' => $this->invitation,
                'user' => Auth::user(),
            ])->layout('layouts.guest');
        }

        // Utilisateur connecté avec email différent
        if (Auth::check()) {
            return view('livewire.family-invitation.wrong-account', [
                'invitation' => $this->invitation,
            ])->layout('layouts.guest');
        }

        // Utilisateur non connecté
        return view('livewire.family-invitation.register', [
            'invitation' => $this->invitation,
            'email' => $this->invitation->email,
        ])->layout('layouts.guest');
    }
}
