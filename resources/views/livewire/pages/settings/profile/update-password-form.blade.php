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

    Toaster::success('Mot de passe mis à jour.');
};

?>

<section class="p-6">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-400" role="heading" aria-level="2">
            {{ __('Mettre à jour le mot de passe') }}
        </h2>

        <p class="text-sm text-gray-600">
            {{ __('Soyez sûr de choisir un mot de passe sécurisé.') }}
        </p>

        <span class="block mt-4 text-sm text-indigo-950 dark:text-pink-500">
            {{ __('Le mot de passe doit contenir au moins 8 caractères.') }}
        </span>
    </div>

    <form wire:submit="updatePassword">
        @csrf

        <div class="mb-8 flex flex-col gap-4">

            <x-form.field-password
                label="Mot de passe actuel"
                name="update_password_current_password"
                model="current_password"
                required
            />

            <x-form.field-password
                label="Nouveau mot de passe"
                name="update_password_password"
                model="password"
                required
            />

            <x-form.field-password
                label="Confirmer le nouveau mot de passe"
                name="update_password_password_confirmation"
                model="password_confirmation"
                required
            />
        </div>

        <div class="flex justify-start mt-6">
            <button type="button"
                    class="mr-4 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Annuler') }}
            </button>

            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('Sauvegarder') }}
            </button>
        </div>
    </form>
</section>
