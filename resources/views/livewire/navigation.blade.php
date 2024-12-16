<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<nav role="navigation"
     aria-label="Navigation principale"
     class="mb-4 flex items-center flex-wrap bg-white-500 p-6 gap-6 border-b border-gray-200"
>
    <a href="{{ route('dashboard') }}" title="Retour à l'accueil" wire:navigate>
        <x-app-logo />
    </a>

    <a href="{{ route('profile') }}" title="Vers le profil" wire:navigate
       class="inline text-inherit font-medium underline-offset-[6px] decoration-zinc-800/20 hover:decoration-current underline text-zinc-800">
        {{ __('Profil') }}
    </a>

    <button type="button" wire:click="logout"
            class="inline text-inherit font-medium underline-offset-[6px] decoration-zinc-800/20 hover:decoration-current underline text-zinc-800">
        {{ __('Se déconnecter') }}
    </button>
</nav>
