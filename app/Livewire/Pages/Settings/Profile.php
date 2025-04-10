<?php

namespace App\Livewire\Pages\Settings;

use App\Livewire\Forms\UserPasswordForm;
use App\Livewire\Forms\UserProfileForm;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Réglages de profil')]
class Profile extends Component
{
    use WithFileUploads;

    public UserProfileForm $form;

    public UserPasswordForm $passwordForm;

    public function mount(): void
    {
        $this->form->setUserData(auth()->user());
    }

    public function cancelProfileEdit(): void
    {
        $this->form->reset('avatar');
        $this->resetForm();
    }

    public function cancelPasswordEdit(): void
    {
        $this->passwordForm->reset(['current_password', 'password', 'password_confirmation']);
        $this->passwordForm->resetValidation();
    }

    public function updatedFormAvatar(): void
    {
        $this->resetErrorBag('form.avatar');

        try {
            if ($this->form->avatar) {
                $this->form->avatar->temporaryUrl();
            }
        } catch (\Exception) {
            $this->addError('form.avatar', "Le format de l'image n'est pas supporté pour l'aperçu");
            $this->form->reset('avatar');
        }
    }

    public function updateProfileInformation(): void
    {
        $this->form->update();
    }

    public function deleteAvatar(): void
    {
        $this->form->deleteAvatar();
    }

    public function sendVerification(): void
    {
        $this->form->sendVerification();
    }

    public function updatePassword(): void
    {
        $this->passwordForm->update();
    }

    private function resetForm(): void
    {
        $this->form->setUserData(auth()->user());
        $this->resetErrorBag();
    }

    public function render()
    {
        $user = Auth::user();

        return view('livewire.pages.settings.profile', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'hasVerifiedEmail' => $user->hasVerifiedEmail(),
        ])->layout('layouts.app-sidebar');
    }
}
