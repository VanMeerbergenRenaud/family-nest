<?php

namespace App\Livewire\Pages\Family;

use App\Enums\FamilyPermissionEnum;
use App\Enums\FamilyRelationEnum;
use App\Livewire\Actions\Logout;
use App\Livewire\Forms\RegisterForm;
use App\Models\FamilyInvitation;
use App\Models\User;
use App\Services\FamilyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class InvitationHandler extends Component
{
    public ?FamilyInvitation $invitation = null;

    public RegisterForm $form;

    public string $token = '';

    public string $password = '';

    public bool $remember = false;

    public bool $userExists = false;

    protected FamilyService $familyService;

    public function boot(FamilyService $familyService): void
    {
        $this->familyService = $familyService;
    }

    public function mount(string $token)
    {
        $this->token = $token;
        $this->invitation = FamilyInvitation::where('token', $token)->first();

        if (! $this->invitation || $this->invitation->isExpired()) {
            $this->redirectRoute('welcome');
        }

        $this->form->email = $this->invitation->email;

        // Vérifier si l'utilisateur existe déjà
        $this->userExists = User::where('email', $this->invitation->email)->exists();

        if (Auth::check() && Auth::user()->email !== $this->invitation->email) {
            Toaster::warning('Attention ! Vous êtes déjà connecté avec un autre compte::Veuillez vous déconnecter pour accepter cette invitation.');
        }
    }

    public function acceptInvitation(): void
    {
        if (! $this->validateInvitation()) {
            $this->redirectRoute('welcome', ['error' => 'Cette invitation n\'est plus valable.']);
        }

        try {
            $this->processInvitation();
            $this->redirectRoute('family');
        } catch (\Exception $e) {
            Log::error('Error while accepting the invitation', ['error' => $e->getMessage()]);
            Toaster::error('Une erreur s\'est produite. L\'invitation n\'a pas pu être acceptée.');
        }
    }

    public function attemptLogin(): void
    {
        $this->validate([
            'password' => 'required',
        ]);

        try {
            $user = User::where('email', $this->invitation->email)->first();

            if (! $user || ! Hash::check($this->password, $user->password)) {
                $this->addError('password', 'Mot de passe incorrect');

                return;
            }

            Auth::login($user, $this->remember);
            Session::regenerate();

            // Traiter l'invitation après la connexion
            $this->processInvitation();

            Toaster::success('Vous avez été ajouté(e) à la famille '.$this->invitation->family->name);
            $this->redirectRoute('family');

        } catch (\Exception $e) {
            Log::error('Error during login and invitation acceptance', ['error' => $e->getMessage()]);
            $this->addError('password', 'Une erreur est survenue lors de la connexion');
        }
    }

    public function register(): void
    {
        if (! $this->validateInvitation()) {
            return;
        }

        try {
            // Si l'utilisateur existe déjà, on ne devrait pas arriver ici
            // puisque la vue login sera affichée, mais vérifions quand même
            if (User::where('email', $this->invitation->email)->exists()) {
                $this->userExists = true;
                Toaster::info('Un compte existe déjà avec cette adresse email. Veuillez vous connecter.');

                return;
            }

            $this->form->redirect_to_onboarding = false;

            $this->form->register();

            $this->processInvitation();

            $this->redirectRoute('verification.notice');
        } catch (\Exception $e) {
            Log::error('Error creating account', ['error' => $e->getMessage()]);
            Toaster::error('Une erreur s\'est produite::Le compte n\'a pas pu être créé.');
        }
    }

    protected function validateInvitation(): bool
    {
        if ($this->invitation->isExpired()) {
            Toaster::error('Cette invitation n\'est plus valable.');

            return false;
        }

        if (Auth::check() && Auth::user()->email !== $this->invitation->email) {
            Toaster::error('Vous êtes déjà connecté::Veuillez vous déconnecter pour accepter cette invitation.');

            return false;
        }

        return true;
    }

    protected function processInvitation(): void
    {
        $permission = FamilyPermissionEnum::tryFrom($this->invitation->permission) ?? FamilyPermissionEnum::Viewer;
        $relation = FamilyRelationEnum::tryFrom($this->invitation->relation) ?? FamilyRelationEnum::Member;

        // Vérifier si l'utilisateur fait déjà partie de cette famille
        $familyCheck = $this->invitation->family->users()->where('user_id', Auth::id())->exists();
        if ($familyCheck) {
            $this->invitation->delete();
            throw new \Exception("L'utilisateur fait déjà partie de cette famille");
        }

        $this->invitation->family->users()->attach(Auth::id(), [
            'permission' => $permission->value,
            'relation' => $relation->value,
            'is_admin' => $permission->isAdmin(),
        ]);

        $this->invitation->delete();
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    public function redirectToWelcome(): void
    {
        $this->redirectRoute('welcome');
    }

    public function render()
    {
        if (! $this->invitation || $this->invitation->isExpired()) {
            return view('livewire.pages.family.invitation-handler.expired')
                ->layout('layouts.guest');
        }

        if (Auth::check()) {
            if (Auth::user()->email === $this->invitation->email) {
                return view('livewire.pages.family.invitation-handler.accept', [
                    'invitation' => $this->invitation,
                    'user' => Auth::user(),
                ])->layout('layouts.guest');
            }

            return view('livewire.pages.family.invitation-handler.wrong-account', [
                'invitation' => $this->invitation,
            ])->layout('layouts.guest');
        }

        // Si l'utilisateur existe déjà mais n'est pas connecté,
        // afficher une vue spécifique pour la connexion
        if ($this->userExists) {
            return view('livewire.pages.family.invitation-handler.login', [
                'invitation' => $this->invitation,
                'email' => $this->invitation->email,
            ])->layout('layouts.guest');
        }

        return view('livewire.pages.family.invitation-handler.register', [
            'invitation' => $this->invitation,
            'email' => $this->invitation->email,
        ])->layout('layouts.guest');
    }
}
