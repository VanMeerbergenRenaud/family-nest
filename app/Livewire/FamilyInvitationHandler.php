<?php

namespace App\Livewire;

use App\Enums\FamilyPermissionEnum;
use App\Enums\FamilyRelationEnum;
use App\Livewire\Actions\Logout;
use App\Livewire\Forms\RegisterForm;
use App\Models\FamilyInvitation;
use App\Services\FamilyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class FamilyInvitationHandler extends Component
{
    public ?FamilyInvitation $invitation = null;

    public RegisterForm $form;

    public string $token = '';

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

    public function register(): void
    {
        if (! $this->validateInvitation()) {
            return;
        }

        try {
            $this->form->register();
            $this->processInvitation();

            $route = config('app.email_verification_enabled', true)
                ? 'verification.notice'
                : 'onboarding.family';

            $this->redirectRoute($route);
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

    public function render()
    {
        if (! $this->invitation || $this->invitation->isExpired()) {
            return view('livewire.family-invitation.expired')
                ->layout('layouts.guest');
        }

        if (Auth::check()) {
            if (Auth::user()->email === $this->invitation->email) {
                return view('livewire.family-invitation.accept', [
                    'invitation' => $this->invitation,
                    'user' => Auth::user(),
                ])->layout('layouts.guest');
            }

            return view('livewire.family-invitation.wrong-account', [
                'invitation' => $this->invitation,
            ])->layout('layouts.guest');
        }

        return view('livewire.family-invitation.register', [
            'invitation' => $this->invitation,
            'email' => $this->invitation->email,
        ])->layout('layouts.guest');
    }
}
