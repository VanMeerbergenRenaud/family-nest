<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<nav role="navigation" style="display: flex; align-items: center; flex-direction: row; gap: 2rem;">
    <a href="{{ route('dashboard') }}" title="Retour à l'accueil" wire:navigate>
        <x-app-logo />
    </a>

    <p>{{ auth()->user()->name }}</p>
    <p>{{ auth()->user()->email }}</p>

    <a href="{{ route('dashboard') }}" title="Vers le tableau de bord" wire:navigate>
        {{ __('Dashboard') }}
    </a>

    <a href="{{ route('profile') }}" title="Vers le profil" wire:navigate>
        {{ __('Profil') }}
    </a>

    <button type="button" wire:click="logout">
        {{ __('Se déconnecter') }}
    </button>
</nav>
