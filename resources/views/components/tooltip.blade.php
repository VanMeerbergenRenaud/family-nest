@props([
    'text',
    'position' => 'right',
    'active' => false,
    'show' => false,
])

@php
    $basicColor = 'bg-white text-gray-700 dark:bg-gray-800 dark:text-gray-400';
    $activeColor = 'bg-indigo-100 text-indigo-600 dark:bg-purple-400 dark:text-indigo-200';
    $arrowClasses = [
        'right' => 'absolute -left-1 top-1/2 -mt-1 border-t border-l border-transparent border-b-transparent border-r-8 border-t-8 border-b-8 border-l-0',
        'left' => 'absolute -right-1 top-1/2 -mt-1 border-t border-r border-transparent border-b-transparent border-l-8 border-t-8 border-b-8 border-r-0',
        'top' => 'absolute left-1/2 -bottom-1 -ml-1 border-l border-r border-transparent border-b-transparent border-t-8 border-l-8 border-r-8 border-b-0',
        'bottom' => 'absolute left-1/2 -top-1 -ml-1 border-l border-r border-transparent border-t-transparent border-b-8 border-l-8 border-r-8 border-t-0',
    ];
    $arrowColor = [
        'right' => $active ? 'border-r-indigo-100 dark:border-r-purple-400' : 'border-r-white dark:border-r-gray-800',
        'left' => $active ? 'border-l-indigo-100 dark:border-l-purple-400' : 'border-l-white dark:border-l-gray-800',
        'top' => $active ? 'border-t-indigo-100 dark:border-t-purple-400' : 'border-t-white dark:border-t-gray-800',
        'bottom' => $active ? 'border-b-indigo-100 dark:border-b-purple-400' : 'border-b-white dark:border-b-gray-800',
    ];
    $positionStyles = [
        'right' => 'left-full ml-2 transform -translate-y-1/2 top-1/2',
        'left' => 'right-full mr-2 transform -translate-y-1/2 top-1/2',
        'top' => 'bottom-full mb-2 transform -translate-x-1/2 left-1/2',
        'bottom' => 'top-full mt-2 transform -translate-x-1/2 left-1/2',
    ];
@endphp

<div x-show="{{ $show }}"
     x-cloak
     class="absolute z-40 px-3 py-2 text-sm font-medium rounded-lg shadow-md whitespace-nowrap bg-white {{ $active ? $activeColor : $basicColor }} {{ $positionStyles[$position] }}"
     {{ $attributes }}
>
    {{ $text }}
</div>
