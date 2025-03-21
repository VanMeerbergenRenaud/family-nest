<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('layouts.guest');

state(['password' => '']);

rules(['password' => ['required', 'string']]);

$confirmPassword = function () {
    $this->validate();

    if (! Auth::guard('web')->validate([
        'email' => Auth::user()->email,
        'password' => $this->password,
    ])) {
        throw ValidationException::withMessages([
            'password' => __('auth.password'),
        ]);
    }

    session(['auth.password_confirmed_at' => time()]);

    $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
};

?>

<x-auth-template title="Mot de passe oublié">
    <div class="text-md-regular text-gray-700 px-4 mt-[-1rem]">

        <!-- Description -->
        <p>
            {{ __('Il s’agit d’une zone sécurisée de l’application. Veuillez confirmer votre mot de passe avant de continuer.') }}
        </p>

        <!-- Formulaire de confirmation -->
        <form wire:submit="confirmPassword" class="mt-4">
            @csrf

            <div class="flex flex-col gap-4">
                <!-- Mot de passe -->
                <x-form.field-password
                    label="Mot de passe"
                    name="password"
                    model="password"
                    autocomplete="current-password"
                    required
                />
            </div>

            <button type="submit" class="mt-4 button-secondary">
                {{ __('Confirmer') }}
            </button>
        </form>
    </div>
</x-auth-template>
