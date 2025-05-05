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
    $isActive = request()->routeIs(basename($href)) || request()->url() === $href;
@endphp

<li class="rounded-lg cursor-pointer {{ $basicColor }}" x-data="{ showTooltip: false }" role="listitem">
    <a href="{{ $href }}"
       wire:navigate
       @mouseenter="showTooltip = true"
       @mouseleave="showTooltip = false"
       class="group flex items-center px-3 py-2 h-10 rounded-lg relative {{ $isActive ? $activeColor : '' }}"
        {{ $attributes }}
    >
        <span :class="{{ $isActive ? 'true' : 'false' }} ? 'text-indigo-600 dark:text-indigo-200' : '{{ $groupColor }}'">
            <x-dynamic-component :component="'svg.' . $icon" class="w-4.5 h-4.5 text-current" />
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
