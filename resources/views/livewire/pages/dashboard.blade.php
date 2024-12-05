<div>
    <p>Vous êtes connecté(e) !</p>
    <p>Bienvenue sur votre tableau de bord <a href="#" class="simple-link">Tailwind css</a>.</p>

    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}"/>

    {{-- Bouton pour ouvrir la sidebar --}}
    <button wire:click="toggleSidebar" aria-expanded="{{ $isSidebarOpen ? 'true' : 'false' }}" aria-controls="sidebar">
        Ouvrir la sidebar
    </button>

    {{-- Sidebar --}}
    <x-sidebar :isSidebarOpen="$isSidebarOpen" title="Mon nouveau titre">
        <x-slot name="content">
            Contenu de la sidebar
        </x-slot>
    </x-sidebar>
</div>
