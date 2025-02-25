@props([
    'sidebarWide' => null,
])

@php
    $spanClass = 'block w-full h-10 text-sm-regular text-gray-500 rounded-lg bg-gray-50 border border-gray-300 placeholder-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 text-left';
@endphp

<button type="button"
        class="cursor-pointer block relative w-full"
        @click="document.dispatchEvent(new KeyboardEvent('keydown', { key: 'k', metaKey: true }))"
>
    {{-- Icon --}}
    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <x-svg.search-classic class="w-5 h-5 text-gray-400 dark:text-gray-500" />
    </span>
    {{-- Text --}}
    @if(!$sidebarWide)
        <span class="lg:hidden p-2.5 pl-10 {{ $spanClass }}">
            Rechercher...
        </span>
    @else
        <span :class="{{ $sidebarWide }} ? 'p-2.5 pl-10' : 'p-2.5 pl-2.5'"
              class="max-lg:hidden {{ $spanClass }}"
              x-text="{{ $sidebarWide }} ? 'Rechercher...' : ''"></span>
        {{-- Shortcut --}}
        <span x-show="{{ $sidebarWide }}" class="absolute inset-y-0 right-0 flex items-center pr-2 max-lg:hidden">
            <x-shortcut key="⌘ K"/>
        </span>
    @endif
</button>
