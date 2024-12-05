<div>
    <p>Vous êtes connecté(e) !</p>
    <p>Bienvenue sur votre tableau de bord <a href="#" class="simple-link">Tailwind css</a>.</p>

    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}"/>

    {{-- Bouton pour ouvrir la sidebar --}}
    <button wire:click="toggleSidebar" aria-expanded="{{ $isSidebarOpen ? 'true' : 'false' }}" aria-controls="sidebar">
        Ouvrir la sidebar
    </button>

    {{-- Sidebar --}}
    <x-sidebar :isSidebarOpen="$isSidebarOpen">
        <x-slot name="header">
            <button wire:click="$set('isSidebarOpen', false)" class="close" aria-label="Fermer la sidebar">
                <x-svg.cross/>
            </button>

            <h2 class="title">Titre de la sidebar</h2>
        </x-slot>

        <x-slot name="content">
            Contenu de la sidebar
        </x-slot>

        <x-slot name="footer">
            <div class="sidebar__container__content__footer__content">
                <x-sidebar.close>
                    <button type="button" class="cancel">
                        Annuler
                    </button>
                </x-sidebar.close>

                <button class="save">
                    Valider
                </button>
            </div>
        </x-slot>
    </x-sidebar>
</div>
