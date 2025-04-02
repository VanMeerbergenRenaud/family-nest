<?php

namespace App\Livewire\Forms;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Masmerise\Toaster\Toaster;

class UserPasswordForm extends Form
{
    #[Validate]
    public string $current_password = '';

    #[Validate]
    public string $password = '';

    #[Validate]
    public string $password_confirmation = '';

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    public function update(): bool
    {
        try {
            $this->validate();

            DB::beginTransaction();

            $user = auth()->user();
            $user->update([
                'password' => Hash::make($this->password),
            ]);

            DB::commit();

            $this->reset(['current_password', 'password', 'password_confirmation']);

            Toaster::success('Mot de passe mis à jour.');

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Toaster::error('Erreur lors de la mise à jour du mot de passe');

            return false;
        }
    }
}
