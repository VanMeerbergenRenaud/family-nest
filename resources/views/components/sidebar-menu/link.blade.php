@props([
    'href',
    'icon',
    'label',
    'expanded' => true,
])

@php
    $basicColor = 'text-gray-700 bg-white dark:text-gray-400 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700';
    $groupColor = 'group-hover:text-gray-900 dark:group-hover:text-gray-100';
    $activeColor = 'text-indigo-600 bg-indigo-100 dark:text-indigo-200 dark:bg-[#7F56D9] hover:bg-gray-100 dark:hover:bg-gray-700';
@endphp

<li class="rounded-lg overflow-hidden {{ $basicColor }}">
    <a href="{{ $href }}"
       wire:current="{{ $activeColor }}"
       class="group flex items-center rounded-lg px-3 py-2 h-10"
    >
        <span class="w-4.5 h-4.5 stroke-2 {{ $groupColor }}">
            <x-dynamic-component :component="'heroicon-o-'.$icon" />
        </span>
        <span x-show="expanded" class="ml-2.5 {{ $groupColor }}">
            {{ $label }}
        </span>
    </a>
</li>
