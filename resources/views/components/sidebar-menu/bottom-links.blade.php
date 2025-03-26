@props([
    'expanded' => true,
])

@php
    $linkClass = 'flex items-center w-fit p-2 py-1.5 text-gray-700 rounded-lg dark:text-gray-400 group relative';
    $activeClass = 'text-indigo-600 dark:text-indigo-500';
    $svgClass = 'w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-50';
@endphp

<ul class="font-medium custom-mt-auto">
    <li x-data="{ showTooltip: false }">
        <button class="{{ $linkClass }}"
                onclick="window.location.reload();"
                @mouseenter="showTooltip = true"
                @mouseleave="showTooltip = false">
            <x-heroicon-o-arrow-path class="{{ $svgClass }}"/>
            @if($expanded)
                <span class="ml-3">Rafraîchir</span>
            @else
                <div x-cloak x-show="showTooltip">
                    <x-tooltip
                        text="Rafraîchir"
                        position="right"
                        show="true"
                    />
                </div>
            @endif
        </button>
    </li>
    <li x-data="{ showTooltip: false }">
        <a href="{{ route('settings.index') }}"
           class="{{ $linkClass }} @if (request()->routeIs('settings.index')) {{ $activeClass }} @endif"
           wire:navigate
           @mouseenter="showTooltip = true"
           @mouseleave="showTooltip = false">
            <x-heroicon-o-cog class="{{ $svgClass }} @if (request()->routeIs('settings.index')) {{ $activeClass }} @endif"/>
            @if($expanded)
                <span class="ml-3">Paramètres</span>
            @else
                <div x-cloak x-show="showTooltip">
                    <x-tooltip
                        text="Paramètres"
                        position="right"
                        :active="request()->routeIs('settings.index')"
                        show="true"
                    />
                </div>
            @endif
        </a>
    </li>
    <li x-data="{ showTooltip: false }">
        <a href="{{ route('help-center') }}"
           class="{{ $linkClass }} @if (request()->routeIs('help-center')) {{ $activeClass }} @endif"
           wire:navigate
           @mouseenter="showTooltip = true"
           @mouseleave="showTooltip = false">
            <x-heroicon-o-question-mark-circle class="{{ $svgClass }} @if (request()->routeIs('help-center')) {{ $activeClass }} @endif"/>
            @if($expanded)
                <span class="ml-3">Centre d'aide</span>
            @else
                <div x-cloak x-show="showTooltip">
                    <x-tooltip
                        text="Centre d'aide"
                        position="right"
                        :active="request()->routeIs('help-center')"
                        show="true"
                    />
                </div>
            @endif
        </a>
    </li>
</ul>
