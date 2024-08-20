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

<div>
    <p>
        {{ __('Il s’agit d’une zone sécurisée de l’application. Veuillez confirmer votre mot de passe avant de continuer.') }}
    </p>

    <form wire:submit="confirmPassword">
        @csrf

        <!-- Password -->
        <div>
            <x-form.field-password
                label="Mot de passe"
                name="password"
                model="password"
                autocomplete="current-password"
                required
            />
        </div>

        <div>
            <button type="submit">
                {{ __('Confirmer') }}
            </button>
        </div>
    </form>
</div>
