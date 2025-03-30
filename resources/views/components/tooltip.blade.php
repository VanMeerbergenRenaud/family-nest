@props([
    'text',
    'position' => 'right',
    'active' => false,
    'show' => false,
    'colorStyle' => 'gray',
])

@php
    $gray = 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400';
    $white = 'bg-white text-gray-700 dark:bg-gray-800 dark:text-gray-400';
    $purple = 'bg-purple-600 text-white dark:bg-purple-400 dark:text-purple-200';
    $activeState = 'bg-indigo-100 text-indigo-600 dark:bg-purple-400 dark:text-indigo-200';

    $positionStyles = [
        'right' => 'left-full ml-2 transform -translate-y-1/2 top-1/2',
        'left' => 'right-full mr-2 transform -translate-y-1/2 top-1/2',
        'top' => 'bottom-full mb-2 transform -translate-x-1/2 left-1/2',
        'bottom' => 'top-full mt-2 transform -translate-x-1/2 left-1/2',
    ];

    $colorClass = match($colorStyle) {
        'white' => $white,
        'purple' => $purple,
        default => $gray
    };
@endphp

<div x-show="{{ $show }}"
     x-cloak
     class="absolute z-40 px-3 py-2 text-sm-medium shadow-md rounded-lg whitespace-nowrap {{ $active ? $activeState : $colorClass }} {{ $positionStyles[$position] }}"
     {{ $attributes }}
>
    {{ $text }}
</div>
