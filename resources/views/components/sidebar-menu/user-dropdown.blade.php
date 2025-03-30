<x-menu>
    {{-- Button --}}
    <x-menu.button
        title="Voir les actions possibles"
        class="-mt-0.5 p-0.5 flex items-center gap-3 w-full overflow-hidden rounded-md px-1 cursor-pointer"
    >
        {{-- Avatar --}}
        <span class="relative flex shrink-0 overflow-hidden h-8 w-8 rounded-lg">
            <img class="object-cover w-full h-full"
                 alt="{{ auth()->user()->name ?? 'Nom inconnu' }}"
                 src="{{ auth()->user()->avatar ?? asset('img/avatar_placeholder.png') }}">
        </span>
        {{-- Name and email --}}
        <div class="grid flex-1 text-left text-sm leading-tight">
            <span class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst(auth()->user()->name) ?? 'Nom inconnu' }}</span>
            <span class="truncate text-xs text-gray-500 dark:text-gray-400">{{ mb_strtolower(auth()->user()->email) ?? 'Adresse mail inconnue' }}</span>
        </div>
        {{-- Arrow double --}}
        <x-svg.arrow-double />
    </x-menu.button>

    {{-- Dropdown list --}}
    <x-menu.items class="w-[calc(100vw-2rem)] !-mt-3 lg:w-55">

        <x-menu.item wire:click="seeProfile">
            <x-svg.user class="group-hover:text-gray-900"/>
            {{ __('Voir mon profil') }}
        </x-menu.item>

        <x-menu.item wire:click="inviteMember">
            <x-svg.user-plus class="group-hover:text-gray-900"/>
            {{ __('Inviter un membre') }}
        </x-menu.item>

        <x-menu.divider />

        <x-menu.item wire:click="seeUpdates">
            <x-svg.changelog class="group-hover:text-gray-900"/>
            {{ __('Mise à jour') }}
        </x-menu.item>

        <x-menu.item wire:click="seeShortcut">
            <x-svg.lightning class="group-hover:text-gray-900"/>
            {{ __('Raccourcis clavier') }}
        </x-menu.item>

        <x-menu.item wire:click="seeSupport">
            <x-svg.help class="group-hover:text-gray-900"/>
            {{ __('Question au support') }}
        </x-menu.item>

        <x-menu.divider />

        <x-menu.item wire:click="seeArchives" class="group hover:text-red-500">
            <x-svg.trash class="group-hover:text-red-500"/>
            {{ __('Mes archives') }}
        </x-menu.item>

        <x-menu.item wire:click="logout" class="group hover:text-red-500">
            <x-svg.logout class="group-hover:text-red-500"/>
            {{ __('Se déconnecter') }}
        </x-menu.item>

    </x-menu.items>
</x-menu>
