@props([
    'expanded' => true,
])

@php
    $linkClass = 'flex items-center p-2 py-1.5 text-gray-700 rounded-lg dark:text-gray-400 group';
    $activeClass = 'text-indigo-600 dark:text-indigo-500';
    $svgClass = 'w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-50';
@endphp

<ul class="font-medium custom-mt-auto">
    <li>
        <a href="#" class="{{ $linkClass }}" onclick="window.location.reload();">
            <x-heroicon-o-arrow-path class="{{ $svgClass }}"/>
            <span x-show="expanded" class="ml-3">Rafraîchir</span>
        </a>
    </li>
    <li>
        <a href="{{ route('settings.index') }}" class="{{ $linkClass }} @if (request()->routeIs('settings.index')) {{ $activeClass }} @endif" wire:navigate>
            <x-heroicon-o-cog class="{{ $svgClass }} @if (request()->routeIs('settings.index')) {{ $activeClass }} @endif"/>
            <span x-show="expanded" class="ml-3">Paramètres</span>
        </a>
    </li>
    <li>
        <a href="{{ route('help-center') }}" class="{{ $linkClass }} @if (request()->routeIs('help-center')) {{ $activeClass }} @endif" wire:navigate>
            <x-heroicon-o-question-mark-circle class="{{ $svgClass }} @if (request()->routeIs('help-center')) {{ $activeClass }} @endif"/>
            <span x-show="expanded" class="ml-3">Centre d'aide</span>
        </a>
    </li>
</ul>
