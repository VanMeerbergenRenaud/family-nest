<div>
    <p>Vous êtes connecté(e) !</p>
    <p>Bienvenue sur votre tableau de bord <a href="#" class="simple-link">Tailwind css</a>.</p>

    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}"/>

    {{-- Lien vers les factures --}}
    <a href="{{ route('invoices') }}" class="simple-link" title="Vers les factures" wire:navigate>
        {{ __('Voir les factures') }}
    </a>

    <a href="{{ route('invoices.create') }}" class="simple-link" title="Ajouter une facture" wire:navigate>
        {{ __('Ajouter une facture') }}
    </a>

    {{-- Bouton pour ouvrir la sidebar --}}
    <button wire:click="toggleSidebar" aria-expanded="{{ $isSidebarOpen ? 'true' : 'false' }}" aria-controls="sidebar" class="px-3 h-8 flex items-center rounded-lg relative text-zinc-500 hover:text-zinc-800 text-zinc-800 hover:bg-zinc-100 bg-zinc-200">
        Ouvrir la sidebar
    </button>

    {{-- Sidebar --}}
    <x-sidebar :isSidebarOpen="$isSidebarOpen">
        <x-slot name="header">
            <button wire:click="$set('isSidebarOpen', false)" class="absolute top-3 right-3 p-2.5 text-0 cursor-pointer rounded-full hover:bg-gray-200" aria-label="Fermer la sidebar">
                <x-svg.cross/>
            </button>

            <h2 class="pr-14 text-xl font-semibold">Titre de la sidebar</h2>
        </x-slot>

        <x-slot name="content">
            <p>Contenu de la sidebar</p>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end flex-wrap gap-2 gap-x-4">
                <x-sidebar.close>
                    <button type="button" class="px-3 h-8 flex items-center rounded-lg relative text-zinc-500 hover:text-zinc-800 text-zinc-800 hover:bg-zinc-100 bg-zinc-200">
                        <div class="flex-1 text-sm font-medium leading-none whitespace-nowrap">
                            Annuler
                        </div>
                    </button>
                </x-sidebar.close>

                <button
                   class="px-3 h-8 flex items-center rounded-lg relative text-zinc-500 hover:text-zinc-800 text-zinc-800 hover:bg-zinc-100 bg-zinc-200">
                    <div class="flex-1 text-sm font-medium leading-none whitespace-nowrap">
                        Valider
                    </div>
                </button>
            </div>
        </x-slot>
    </x-sidebar>
</div>
