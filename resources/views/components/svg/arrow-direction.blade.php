@props(['direction' => 'right', 'color' => 'gray-500'])

@php
    $rotationClasses = [
        'right' => 'rotate-0',
        'down' => 'rotate-90',
        'left' => 'rotate-180',
        'up' => 'rotate-270',
    ];

    $rotation = $rotationClasses[$direction] ?? 'rotate-0';
@endphp

<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
     class="w-4 h-4 ml-auto transition-transform duration-200 {{ $rotation }} text-{{ $color }} dark:text-{{ str_replace('500', '400', $color) }}">
    <path d="m9 18 6-6-6-6"></path>
</svg>
