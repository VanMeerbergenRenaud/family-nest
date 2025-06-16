@props([
    'expanded' => true,
])

@php
    $linkClass = 'flex items-center w-fit p-2 py-1.5 text-gray-700 rounded-lg dark:text-gray-400 group relative';
    $svgClass = 'w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-50';
    $activeClass = 'text-indigo-600 dark:text-indigo-500 hover:text-indigo-600';
@endphp

<ul class="font-medium custom-mt-auto" role="list">
    <li x-data="{ showTooltip: false }" role="listitem">
        <button class="{{ $linkClass }}"
                @click="window.location.reload();"
                @mouseenter="showTooltip = true"
                @mouseleave="showTooltip = false">
            <svg class="{{ $svgClass }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
            </svg>
            @if($expanded)
                <span class="ml-3">Rafraîchir</span>
            @else
                <div x-cloak x-show="showTooltip">
                    <x-tooltip
                        text="Rafraîchir"
                        position="right"
                        show="true"
                        colorStyle="white"
                    />
                </div>
            @endif
        </button>
    </li>
    <li x-data="{ showTooltip: false }">
        <a href="{{ route('settings.index') }}"
           wire:navigate
           @mouseenter="showTooltip = true"
           @mouseleave="showTooltip = false"
           class="{{ $linkClass }} @if (request()->routeIs('settings.index')) {{ $activeClass }} @endif"
        >
            <svg class="{{ $svgClass }} @if (request()->routeIs('settings.index')) {{ $activeClass }} @endif"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0 0 15 0m-15 0a7.5 7.5 0 1 1 15 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077 1.41-.513m14.095-5.13 1.41-.513M5.106 17.785l1.15-.964m11.49-9.642 1.149-.964M7.501 19.795l.75-1.3m7.5-12.99.75-1.3m-6.063 16.658.26-1.477m2.605-14.772.26-1.477m0 17.726-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205 12 12m6.894 5.785-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495"/>
            </svg>

            @if($expanded)
                <span class="ml-3">Paramètres</span>
            @else
                <div x-cloak x-show="showTooltip">
                    <x-tooltip
                        text="Paramètres"
                        position="right"
                        :active="request()->routeIs('settings.index')"
                        show="true"
                        colorStyle="white"
                    />
                </div>
            @endif
        </a>
    </li>
    <li x-data="{ showTooltip: false }">
        <a href="{{ route('help-center') }}"
           wire:navigate
           @mouseenter="showTooltip = true"
           @mouseleave="showTooltip = false"
           class="{{ $linkClass }} @if (request()->routeIs('help-center')) {{ $activeClass }} @endif"
        >
            <svg class="{{ $svgClass }} @if (request()->routeIs('help-center')) {{ $activeClass }} @endif"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"/>
            </svg>

            @if($expanded)
                <span class="ml-3">Centre d'aide</span>
            @else
                <div x-cloak x-show="showTooltip">
                    <x-tooltip
                        text="Centre d'aide"
                        position="right"
                        :active="request()->routeIs('help-center')"
                        show="true"
                        colorStyle="white"
                    />
                </div>
            @endif
        </a>
    </li>
</ul>
