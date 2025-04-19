<?php

namespace App\Livewire;

use App\Enums\FamilyPermissionEnum;
use App\Enums\FamilyRelationEnum;
use App\Livewire\Actions\Logout;
use App\Livewire\Forms\RegisterForm;
use App\Models\FamilyInvitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class FamilyInvitationHandler extends Component
{
    public ?FamilyInvitation $invitation = null;

    public RegisterForm $form;

    public string $token = '';

    public function mount($token)
    {
        $this->token = $token;
        $this->invitation = FamilyInvitation::where('token', $token)->first();

        // Check if the invitation exists and is not expired
        if (! $this->invitation || $this->invitation->isExpired()) {
            $this->redirectRoute('welcome');
        }

        // Initialize the email in the form
        $this->form->email = $this->invitation->email;

        // Check if the logged-in user has a different email than the invitation
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

    private function validateInvitation(): bool
    {
        if (! $this->invitation || $this->invitation->isExpired()) {
            Toaster::error('Cette invitation n\'est plus valable.');

            return false;
        }

        if (Auth::check() && Auth::user()->email !== $this->invitation->email) {
            Toaster::error('Vous êtes déjà connecté::Veuillez vous déconnecter pour accepter cette invitation.');

            return false;
        }

        return true;
    }

    private function processInvitation(): void
    {
        // Retrieve invitation data and convert it to enums.
        $permissionValue = $this->invitation->permission ?? FamilyPermissionEnum::Viewer->value;
        $relationValue = $this->invitation->relation ?? FamilyRelationEnum::Member->value;

        // Attempt to create enum instances from the retrieved values.
        $permission = FamilyPermissionEnum::tryFrom($permissionValue);
        $relation = FamilyRelationEnum::tryFrom($relationValue);

        // If the enum values are not valid, use default enum values.
        if (! $permission) {
            $permission = FamilyPermissionEnum::Viewer;
        }

        if (! $relation) {
            $relation = FamilyRelationEnum::Member;
        }

        // Check if the user has admin privileges based on the enum value.
        $isAdmin = $permission->isAdmin();

        // Attach the user to the family with the specified permission, relation, and admin status.
        $this->invitation->family->users()->attach(Auth::id(), [
            'permission' => $permission->value,
            'relation' => $relation->value,
            'is_admin' => $isAdmin,
        ]);

        // Delete the processed invitation.
        $this->invitation->delete();
    }

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        // Expired invitation
        if (! $this->invitation || $this->invitation->isExpired()) {
            return view('livewire.family-invitation.expired')
                ->layout('layouts.guest');
        }

        // Logged-in user with matching email
        if (Auth::check() && Auth::user()->email === $this->invitation->email) {
            return view('livewire.family-invitation.accept', [
                'invitation' => $this->invitation,
                'user' => Auth::user(),
            ])->layout('layouts.guest');
        }

        // Logged-in user with different email
        if (Auth::check()) {
            return view('livewire.family-invitation.wrong-account', [
                'invitation' => $this->invitation,
            ])->layout('layouts.guest');
        }

        // Non-logged-in user
        return view('livewire.family-invitation.register', [
            'invitation' => $this->invitation,
            'email' => $this->invitation->email,
        ])->layout('layouts.guest');
    }
}
