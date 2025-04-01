<?php

namespace App\Livewire\Pages\Settings;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

class Profile extends Component
{
    use WithFileUploads;

    public $name;

    public $email;

    #[Validate('nullable|image|max:1024|mimes:jpg,jpeg,png,gif')]
    public $avatar;

    public $avatarUrl;

    public $avatarError = false;

    public $current_password = '';

    public $password = '';

    public $password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->avatarUrl = $user->avatar_url;
    }

    public function cancelProfileEdit(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->reset('avatar');
        $this->resetErrorBag();
    }

    public function cancelPasswordEdit(): void
    {
        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->resetErrorBag();
    }

    public function updatedAvatar(): void
    {
        $this->resetErrorBag('avatar');
        $this->avatarError = false;

        try {
            if ($this->avatar) {
                $this->avatar->temporaryUrl();
            }
        } catch (\Exception $e) {
            $this->avatarError = true;
            $this->addError('avatar', "Le format de l'image n'est pas supporté pour l'aperçu");
            $this->reset('avatar');
        }
    }

    public function updateProfileInformation(): void
    {
        if ($this->avatarError) {
            $this->addError('avatar', "L'image contient des erreurs et ne peut pas être importée");
            $this->reset('avatar');

            return;
        }

        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'avatar' => ['nullable', 'image', 'max:1024', 'mimes:jpg,jpeg,png,gif'],
        ]);

        $emailChanged = $user->email !== $validated['email'];

        $dataToUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($emailChanged) {
            $dataToUpdate['email_verified_at'] = null;
        }

        $user->update($dataToUpdate);

        // Rafraîchir l'URL de l'avatar
        $this->avatarUrl = $user->fresh()->avatar_url;

        // Améliorez la gestion de l'upload d'avatar
        if ($this->avatar) {
            try {
                // Supprimer l'ancien avatar si existe
                $this->deleteOldAvatar($user);

                // Générer un nom de fichier unique
                $directory = "avatars/user_{$user->id}/";
                $filename = $directory . Str::random(40) . '.' . $this->avatar->getClientOriginalExtension();

                // S'assurer que le répertoire existe (en créant un fichier vide comme marqueur)
                if (!Storage::disk('s3')->exists($directory . '/.init')) {
                    Storage::disk('s3')->put($directory . '/.init', '');
                }

                // Mettre à jour l'avatar de l'utilisateur avec le chemin complet
                $dataToUpdate['avatar'] = $this->avatar->storeAs('', $filename, 's3');

                // Réinitialiser l'upload
                $this->reset('avatar');
            } catch (\Exception $e) {
                logger('Erreur avatar: ' . $e->getMessage());
                Toaster::error("Erreur lors du téléchargement de l'avatar: " . $e->getMessage());
                return;
            }
        }

        // Mettre à jour l'utilisateur
        $user->update($dataToUpdate);

        // Rafraîchir l'URL de l'avatar
        $this->avatarUrl = $user->fresh()->avatar_url;

        // Envoyer automatiquement un e-mail de vérification si l'adresse a changé
        if ($emailChanged && $user instanceof MustVerifyEmail) {
            Toaster::success('Profil mis à jour !::Un e-mail de vérification a été envoyé à votre nouvelle adresse.');
            $user->sendEmailVerificationNotification();
        } else {
            Toaster::success('Profil mis à jour !');
        }
    }

    public function deleteAvatar(): void
    {
        $user = Auth::user();
        $this->deleteOldAvatar($user);

        $user->update(['avatar' => null]);
        $this->avatarUrl = null;

        Toaster::success('Avatar supprimé.');
    }

    private function deleteOldAvatar(User $user): void
    {
        if ($user->avatar && Storage::disk('s3')->exists($user->avatar)) {
            Storage::disk('s3')->delete($user->avatar);
        }
    }

    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            Toaster::info('Votre adresse e-mail est déjà vérifiée.');

            return;
        }

        $user->sendEmailVerificationNotification();
        Toaster::success('Un e-mail de vérification a été envoyé à votre adresse e-mail.');
    }

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        Toaster::success('Mot de passe mis à jour.');
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
