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

<div>
    <form wire:submit="register">
        @csrf

        <!-- Name -->
        <div>
            <x-form.field
                label="Nom"
                name="name"
                model="form.name"
                placeholder="John Doe"
                autocomplete="name"
                autofocus
                required
            />
        </div>

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
                autocomplete="new-password"
                required
            />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-form.field-password
                label="Confirmer le mot de passe"
                name="password_confirmation"
                model="form.password_confirmation"
                autocomplete="new-password"
                required
            />
        </div>

        <div>
            <a href="{{ route('login') }}" class="simple-link" title="Vers la page de connexion" wire:navigate>
                {{ __('Déjà inscrit ?') }}
            </a>

            <button type="submit">
                {{ __('S’inscrire') }}
            </button>
        </div>
    </form>
</div>
