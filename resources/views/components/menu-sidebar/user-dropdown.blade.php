@props([
    'sidebarWide' => false,
    'items' => [],
    'user',
])

@if($sidebarWide === "false")
    {{-- Menu Mobile (sm) --}}
    <x-menu class="relative">
        <x-menu.button class="flex w-full items-center gap-3 overflow-hidden rounded-md px-1 cursor-pointer">
        <span class="relative flex shrink-0 overflow-hidden h-8 w-8 rounded-lg">
            <img class="object-cover w-full h-full"
                 src="{{ $user->avatar_url ?? asset('img/avatar_placeholder.png') }}" alt="{{ $user->name }}">
        </span>
            <div class="grid flex-1 text-left text-sm leading-tight">
                <span class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</span>
                <span class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
            </div>

            <x-svg.arrow-direction direction="down"/>
        </x-menu.button>

        <x-menu.items class="w-full min-w-50 max-w-55 -mt-6.5 shadow-lg">
            @foreach ($items as $item)
                <x-menu.item wire:click="{{ $item['action'] }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                    <x-dynamic-component :component="$item['icon']"/>
                    {{ __($item['label']) }}
                </x-menu.item>
            @endforeach
            <x-divider/>
            <x-menu.item wire:click="logout" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                <x-svg.logout/>
                {{ __('Se déconnecter') }}
            </x-menu.item>
        </x-menu.items>
    </x-menu>
@else
    {{-- Menu Desktop (lg) --}}
    <x-menu class="relative">
        <x-menu.button
            class="flex w-full items-center gap-3 overflow-hidden rounded-md px-1 cursor-pointer">
            <span class="relative flex shrink-0 overflow-hidden h-8 w-8 rounded-lg">
                <img class="object-cover w-full h-full" src="{{ $user->avatar_url ?? asset('img/avatar_placeholder.png') }}" alt="{{ $user->name }}">
            </span>
            <div class="grid flex-1 text-left text-sm leading-tight">
                <span class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</span>
                <span class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
            </div>

            <x-svg.arrow-direction direction="down"/>
        </x-menu.button>

        <x-menu.items class="w-full min-w-50 max-w-86 -mt-8 shadow-lg">
            @foreach ($items as $item)
                <x-menu.item wire:click="{{ $item['action'] }}"
                             class="block px-4 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                    <x-dynamic-component :component="$item['icon']"/>
                    {{ __($item['label']) }}
                </x-menu.item>
            @endforeach
            <x-divider/>
            <x-menu.item wire:click="logout" class="block px-4 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                <x-svg.logout/>
                {{ __('Se déconnecter') }}
            </x-menu.item>
        </x-menu.items>
    </x-menu>
@endif
