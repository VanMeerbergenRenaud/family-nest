<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('layouts.guest');

state('token')->locked();

state([
    'email' => fn () => request()->string('email')->value(),
    'password' => '',
    'password_confirmation' => ''
]);

rules([
    'token' => ['required'],
    'email' => ['required', 'string', 'email', 'lowercase', 'max:255'],
    'password' => ['required', 'string', 'min:8', 'max:255'],
    'password_confirmation' => ['required', 'string', 'min:8', 'max:255', 'same:password'],
]);

$resetPassword = function () {
    $this->validate();

    // Here we will attempt to reset the user's password. If it's successful, we
    // will update the password on an actual user model and persist it to the
    // database. Otherwise, we will parse the error and return the response.
    $status = Password::reset(
        $this->only('email', 'password', 'password_confirmation', 'token'),
        function ($user) {
            $user->forceFill([
                'password' => Hash::make($this->password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        }
    );

    // If the password was successfully reset, we will redirect the user back to
    // the application's home authenticated view. If there is an error, we can
    // redirect them back to where they came from with their error message.
    if ($status != Password::PASSWORD_RESET) {
        $this->addError('email', __($status));

        return;
    }

    Session::flash('status', __($status));

    $this->redirectRoute('login', navigate: true);
};

?>

<x-auth-template title="Réinitialisation du mot de passe">

    <!-- Formulaire de réinitialisation -->
    <form wire:submit="resetPassword">
        @csrf

        <div class="flex flex-col gap-4">

            <!-- Adresse mail -->
            <x-form.field
                label="Adresse e-mail"
                name="email"
                type="email"
                model="email"
                placeholder="john.doe@gmail.com"
                autocomplete="email"
                autofocus
                required
            />

            <!-- Mot de passe -->
            <x-form.field-password
                label="Mot de passe"
                name="password"
                model="password"
                autocomplete="new-password"
                required
            />

            <!-- Confirmation du mot de passe -->
            <x-form.field-password
                label="Confirmer le mot de passe"
                name="password_confirmation"
                model="password_confirmation"
                autocomplete="new-password"
                required
            />
        </div>

        <button type="submit" class="mt-8 px-4 py-2.5 rounded-md text-sm-medium bg-[#364153] text-gray-100">
            {{ __('Réinitialiser le mot de passe') }}
        </button>
    </form>
</x-auth-template>
