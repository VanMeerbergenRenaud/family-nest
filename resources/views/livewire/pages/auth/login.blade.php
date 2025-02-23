<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\form;
use function Livewire\Volt\layout;

layout('layouts.guest');

form(LoginForm::class);

$login = function () {
    $this->validate();

    $this->form->authenticate();

    Session::regenerate();

    $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
};

?>

<x-auth-template title="Connexion" description="Entrez vos information pour vous connectez." showSocialLogin>

    <!-- Session Status -->
    @if (session('status'))
        <p class="mt-4 mb-6 bg-green-50 border border-green-200 py-2 px-4 gap-4 rounded-md text-sm text-green-700 dark:bg-green-100 dark:border-green-300 dark:text-green-600 flex items-center">
            <x-svg.success class="h-4 w-4" />
            {{ session('status') }}
        </p>
    @endif

    <!-- Formulaire d'inscription -->
    <form wire:submit="login">
        @csrf

        <div class="flex flex-col gap-4">
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
                autocomplete="current-password"
                required
            />
        </div>

        <div class="mt-6 md:px-2 flex items-center justify-between">
            <x-form.checkbox-input name="remember-me" model="form.remember" label="Se souvenir de moi" />
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="ml-3 text-sm-medium underline" title="Vers la page de réinitialisation de mot de passe" wire:navigate>
                    {{ __("Mot de passe oublié ?") }}
                </a>
            @endif
        </div>

        <div class="mt-8 mb-5">
            <button type="submit" class="w-full py-3.5 px-10 rounded-lg text-sm-medium md:text-[15px] bg-[#292A2B] text-white hover:bg-black">
                {{ __("Se connecter") }}
            </button>
        </div>

        <!-- Lien inscription -->
        @if (Route::has('register'))
            <div class="text-sm-regular text-center text-dark-gray">
                {{ __("Pas encore membre ?") }}
                <a href="{{ route('register') }}"
                   class="text-sm-medium text-black custom-underline-link"
                   title="Vers la page de connexion" wire:navigate>
                    {{ __("Créer un compte") }}
                </a>
            </div>
        @endif
    </form>
</x-auth-template>
