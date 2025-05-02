<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

class UserProfileForm extends Form
{
    use WithFileUploads;

    #[Validate]
    public string $name = '';

    #[Validate]
    public string $email = '';

    #[Validate]
    public $avatar;

    public $avatarUrl;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore(auth()->id()),
            ],
            'avatar' => ['nullable', 'image', 'max:1024', 'mimes:jpg,jpeg,png'],
        ];
    }

    public function setUserData(User $user): void
    {
        $this->fill([
            'name' => $user->name,
            'email' => $user->email,
            'avatarUrl' => $user->avatar_url,
        ]);
    }

    public function update(): bool
    {
        $this->validate();
        $user = auth()->user();
        $emailChanged = $user->email !== $this->email;

        try {
            DB::beginTransaction();

            $user->fill([
                'name' => $this->name,
                'email' => $this->email,
                'email_verified_at' => $emailChanged ? null : $user->email_verified_at,
            ]);

            if ($this->avatar) {
                $this->uploadAvatar($user);
            }

            $user->save();
            $this->avatarUrl = $user->fresh()->avatar_url;

            DB::commit();

            if ($emailChanged && $user instanceof MustVerifyEmail) {
                $user->sendEmailVerificationNotification();
                Toaster::success('Profil mis à jour !::Un e-mail de vérification a été envoyé à votre nouvelle adresse.');
            } else {
                Toaster::success('Profil mis à jour !');
            }

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Toaster::error('Erreur lors de la mise à jour du profil');
            \Log::error('Erreur lors de la mise à jour du profil', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function deleteAvatar(): bool
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();
            $this->deleteOldAvatar($user);

            $user->update(['avatar' => null]);
            $this->avatarUrl = null;

            DB::commit();
            Toaster::success('Avatar supprimé.');

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Toaster::error("Erreur lors de la suppression de l'avatar");

            return false;
        }
    }

    private function uploadAvatar(User $user): void
    {
        // Supprimer l'ancien avatar
        $this->deleteOldAvatar($user);

        // Générer un nouveau chemin de fichier
        $directory = "avatars/user_{$user->id}/";
        $filename = $directory.Str::random(40).'.'.$this->avatar->getClientOriginalExtension();

        if (! Storage::disk('s3')->exists($directory.'/.init')) {
            Storage::disk('s3')->put($directory.'/.init', '');
        }

        // Mettre à jour l'avatar de l'utilisateur avec le chemin complet
        $user['avatar'] = $this->avatar->storeAs('', $filename, 's3');

        $this->reset('avatar');
    }

    private function deleteOldAvatar(User $user): void
    {
        if ($user->avatar && Storage::disk('s3')->exists($user->avatar)) {
            Storage::disk('s3')->delete($user->avatar);
        }
    }

    public function sendVerification(): void
    {
        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            Toaster::info('Votre adresse e-mail est déjà vérifiée.');

            return;
        }

        $user->sendEmailVerificationNotification();
        Toaster::success('Un e-mail de vérification a été envoyé à votre adresse e-mail.');
    }
}
