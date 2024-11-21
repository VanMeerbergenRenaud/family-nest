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

<div>
    <!-- Session Status -->
    @if (session('status'))
        <div>
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="login">
        @csrf

        <!-- Email Address -->
        <div>
            <x-form.field
                label="Adresse e-mail"
                name="email"
                type="email"
                model="form.email"
                placeholder="john.doe@gmail.com"
                autocomplete="email"
                required
            />
        </div>

        <!-- Password -->
        <div>
            <x-form.field-password
                label="Mot de passe"
                name="password"
                model="form.password"
                autocomplete="current-password"
                required
            />
        </div>

        <!-- Remember Me -->
        <div>
            <label for="remember">
                <input wire:model="form.remember" id="remember" type="checkbox">
                <span>{{ __('Se souvenir de moi') }}</span>
            </label>
        </div>

        <div>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="simple-link" title="Vers la page d’inscription" wire:navigate>
                    {{ __('Pas encore de compte ?') }}
                </a>
            @endif

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="simple-link" title="Vers la page de réinitialisation du mot de passe" wire:navigate>
                    {{ __('Mot de passe oublié ?') }}
                </a>
            @endif

            <button type="submit">
                {{ __('Se connecter') }}
            </button>
        </div>
    </form>
</div>
