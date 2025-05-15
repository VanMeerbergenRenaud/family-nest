@props([
    'user',
    'expanded' => true,
])

<div class="relative" x-data="{ showTooltip: false }">
    <x-menu>
        {{-- Button --}}
        <x-menu.button
            title="Voir les actions possibles"
            @mouseenter="showTooltip = true"
            @mouseleave="showTooltip = false"
            class="-mt-0.5 p-0.5 flex items-center gap-3 w-full overflow-hidden rounded-md px-1 cursor-pointer"
        >
            {{-- Avatar --}}
            <span class="relative flex shrink-0 overflow-hidden h-8 w-8 rounded-lg">
                <img class="object-cover w-full h-full"
                     src="{{ $user->avatar_url ?? asset('img/avatar_placeholder.png') }}"
                     alt="{{ $user->name ?? 'Nom inconnu' }}"
                     loading="lazy"
                >
            </span>

            @if($expanded)
                {{-- Name and email --}}
                <div class="grid flex-1 text-left text-sm leading-tight">
                    <span class="truncate text-sm-semibold text-gray-900 dark:text-white capitalize">
                        {{ $user->name ?? 'Nom inconnu' }}
                    </span>
                    <span class="truncate text-xs text-gray-500 dark:text-gray-400 lowercase">
                        {{ $user->email ?? 'Adresse email inconnue' }}
                    </span>
                </div>

                <x-svg.arrow-double/>
            @else
                <div x-cloak x-show="showTooltip">
                    <x-tooltip
                        text="Profil utilisateur"
                        position="right"
                        show="true"
                        colorStyle="white"
                    />
                </div>
            @endif
        </x-menu.button>

        {{-- Dropdown list --}}
        <x-menu.items class="w-[calc(100vw-2rem)] !-mt-3 lg:w-55">
            <x-menu.item wire:click="seeProfile">
                <x-svg.user class="w-4.5 h-4.5 group-hover:text-gray-900 stroke-2"/>
                {{ __('Modifier mon profil') }}
            </x-menu.item>

            <x-menu.item wire:click="inviteMember">
                <x-svg.user-plus class="w-4.5 h-4.5 group-hover:text-gray-900 stroke-2"/>
                {{ __('Inviter un membre') }}
            </x-menu.item>

            <x-menu.divider/>

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

            <x-menu.divider/>

            <x-menu.item wire:click="logout" class="group hover:text-red-500">
                <x-svg.logout class="group-hover:text-red-500"/>
                {{ __('Se déconnecter') }}
            </x-menu.item>

            <x-menu.item wire:click="suppressAccount" class="group hover:text-red-500">
                <x-svg.trash class="group-hover:text-red-500"/>
                {{ __('Supprimer le compte') }}
            </x-menu.item>
        </x-menu.items>
    </x-menu>
</div>
