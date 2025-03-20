@props([
    'href',
    'icon',
    'label',
    'expanded' => true,
])

@php
    $basicColor = 'text-gray-700 bg-white dark:text-gray-400 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700';
    $groupColor = 'group-hover:text-gray-900 dark:group-hover:text-gray-100';
    $activeColor = 'text-indigo-600 bg-indigo-100 dark:text-indigo-200 dark:bg-purple-400 hover:bg-gray-100 dark:hover:bg-gray-700';
@endphp

<li class="rounded-lg {{ $basicColor }}">
    <a href="{{ $href }}"
       wire:current="{{ $activeColor }}"
       wire:navigate
       class="group flex items-center px-3 py-2 h-10 rounded-lg"
       {{ $attributes }}
    >
        <span class="stroke-2 {{ $groupColor }}">
            <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-4.5 h-4.5 text-current" />
        </span>
        <span x-show="expanded" class="ml-2.5 {{ $groupColor }}">
            {{ $label }}
        </span>
    </a>
</li>
