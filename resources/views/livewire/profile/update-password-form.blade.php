<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

use function Livewire\Volt\rules;
use function Livewire\Volt\state;

state([
    'current_password' => '',
    'password' => '',
    'password_confirmation' => ''
]);

rules([
    'current_password' => ['required', 'string', 'current_password'],
    'password' => ['required', 'string', Password::defaults(), 'confirmed'],

]);

$updatePassword = function () {
    try {
        $validated = $this->validate();
    } catch (ValidationException $e) {
        $this->reset('current_password', 'password', 'password_confirmation');

        throw $e;
    }

    Auth::user()->update([
        'password' => Hash::make($validated['password']),
    ]);

    $this->reset('current_password', 'password', 'password_confirmation');

    $this->dispatch('password-updated');
};

?>

<section>
    <header>
        <h2 role="heading" aria-level="2">
            {{ __('Mettre à jour le mot de passe') }}
        </h2>
        <p>
            {{ __('Soyez sûr de choisir un mot de passe sécurisé.') }}
        </p>
        <span>
            {{ __('Le mot de passe doit contenir au moins 8 caractères.') }}
        </span>
    </header>

    <form wire:submit="updatePassword">
        @csrf

        <div>
            <x-form.field-password
                label="Mot de passe actuel"
                name="update_password_current_password"
                model="current_password"
                required
            />
        </div>

        <div>
            <x-form.field-password
                label="Nouveau mot de passe"
                name="update_password_password"
                model="password"
                required
            />
        </div>

        <div>
            <x-form.field-password
                label="Confirmer le nouveau mot de passe"
                name="update_password_password_confirmation"
                model="password_confirmation"
                required
            />
        </div>

        <div>
            <button type="submit">{{ __('Sauvegarder') }}</button>

            <x-action-message on="password-updated">
                {{ __('Mot de passe mis à jour.') }}
            </x-action-message>
        </div>
    </form>
</section>
