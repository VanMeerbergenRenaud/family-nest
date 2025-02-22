@props([
    'route',
    'name',
    'menuVariable',
])

@php
    $classes = "flex items-center px-3 py-2 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group";
    $iconClasses = "w-4.5 h-4.5 stroke-2 text-[#344054] dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white";
    $nameClasses = "ml-2.5 text-[#344054] dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white link-name";
@endphp

<a href="{{ route($route) }}" {{ $attributes->merge(['class' => $classes]) }} wire:navigate>
    <span {{ $attributes->merge(['class' => $iconClasses]) }}>
        {{ $slot }}
    </span>
    <span x-show="{{ $menuVariable }}" {{ $attributes->merge(['class' => $nameClasses]) }}>
        {{ $name }}
    </span>
</a>
