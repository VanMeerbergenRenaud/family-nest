@props([
    'href',
    'icon',
    'label',
    'expanded' => true,
])

@php
    $basicColor = 'text-gray-700 bg-white dark:text-gray-400 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700';
    $groupColor = 'group-hover:text-gray-900 dark:group-hover:text-gray-100';
    $activeColor = 'text-indigo-600 bg-indigo-100 dark:text-indigo-200 dark:bg-purple-400';
    $isActive = request()->routeIs(substr($href, strrpos($href, '/') + 1)) || request()->url() === $href;
@endphp

<li class="rounded-lg {{ $basicColor }}" x-data="{ showTooltip: false }">
    <a href="{{ $href }}"
       class="group flex items-center px-3 py-2 h-10 rounded-lg relative {{ $isActive ? $activeColor : '' }}"
       wire:navigate
       @mouseenter="showTooltip = true"
       @mouseleave="showTooltip = false"
        {{ $attributes }}
    >
        <span class="stroke-2" :class="{{ $isActive ? 'true' : 'false' }} ? 'text-indigo-600 dark:text-indigo-200' : '{{ $groupColor }}'">
            <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-4.5 h-4.5 text-current" />
        </span>
        @if($expanded)
            <span class="ml-2.5" :class="{{ $isActive ? 'true' : 'false' }} ? 'text-indigo-600 dark:text-indigo-200' : '{{ $groupColor }}'">
                {{ $label }}
            </span>
        @endif

        <!-- Tooltip seulement quand sidebar rétrécie -->
        @if(!$expanded)
            <div x-cloak x-show="showTooltip">
                <x-tooltip
                    :text="$label"
                    position="right"
                    :active="$isActive"
                    show="true"
                />
            </div>
        @endif
    </a>
</li>
