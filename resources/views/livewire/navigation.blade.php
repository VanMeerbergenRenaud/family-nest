<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<nav role="navigation"
     aria-label="Navigation principale"
     class="flex items-center flex-wrap bg-white-500 p-6 gap-6 border-b border-gray-200"
>
    <a href="{{ route('dashboard') }}" title="Retour à l'accueil" wire:navigate>
        <x-app-logo class="dark:fill-amber-50"/>
    </a>

    <a href="{{ route('profile') }}" title="Vers le profil" wire:navigate
       class="inline text-gray-800 font-medium underline-offset-[6px] decoration-zinc-800/20 hover:decoration-current underline dark:text-amber-50">
        {{ __('Profil') }}
    </a>

    <button type="button" wire:click="logout"
            class="inline text-gray-800 font-medium underline-offset-[6px] decoration-zinc-800/20 hover:decoration-current underline dark:text-amber-50">
        {{ __('Se déconnecter') }}
    </button>
</nav>
