<?php

use App\Livewire\Forms\RegisterForm;

use function Livewire\Volt\form;
use function Livewire\Volt\layout;

layout('layouts.guest');

form(RegisterForm::class);

$register = function () {
    $this->validate();

    $this->form->register();

    $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
};

?>

<x-auth-template title="Inscription" description="Entrez vos informations pour vous créer un compte." showSocialLogin>

    <!-- Formulaire d'inscription -->
    <form wire:submit="register">
        @csrf

        <div class="flex flex-col gap-4">
            <!-- Name -->
            <x-form.field
                label="Nom"
                name="name"
                model="form.name"
                placeholder="John Doe"
                autocomplete="name"
                autofocus
                required
            />

            <!-- Email -->
            <x-form.field
                label="Adresse e-mail"
                name="email"
                type="email"
                model="form.email"
                placeholder="john.doe@gmail.com"
                autocomplete="email"
                required
            />

            <!-- Password -->
            <x-form.field-password
                label="Mot de passe"
                name="password"
                model="form.password"
                autocomplete="new-password"
                required
            />

            <!-- Confirm Password -->
            {{--<div>
                <x-form.field-password
                    label="Confirmer le mot de passe"
                    name="password_confirmation"
                    model="form.password_confirmation"
                    autocomplete="new-password"
                    required
                />
            </div>--}}
        </div>

        <div class="mt-6 md:px-2 flex items-center justify-between">
            <x-form.checkbox-input name="remember-me" model="form.remember" label="Se souvenir de moi" />
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="ml-3 text-sm-medium underline" title="Vers la page de réinitialisation de l'adresse mail" wire:navigate>
                    {{ __('Adresse mail oubliée ?') }}
                </a>
            @endif
        </div>

        <div class="mt-8 mb-5">
            <button type="submit" class="w-full py-3.5 px-10 rounded-lg text-sm-medium md:text-[15px] bg-[#292A2B] text-white hover:bg-black">
                {{ __('S\'inscrire') }}
            </button>
        </div>

        <!-- Lien connexion -->
        @if (Route::has('login'))
            <div class="text-sm-regular text-center text-dark-gray">
                {{ __('Déjà un compte ?') }}
                <a href="{{ route('login') }}"
                   class="text-sm-medium text-black custom-underline-link"
                   title="Vers la page de connexion" wire:navigate>
                    {{ __('Se connecter') }}
                </a>
            </div>
        @endif
    </form>
</x-auth-template>
