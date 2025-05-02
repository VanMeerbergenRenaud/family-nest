@props([
    'expanded' => true,
])

@php
    $spanClass = 'block w-full h-10 text-sm-regular text-left text-gray-500 rounded-lg bg-gray-50 border border-gray-300 placeholder-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400';
@endphp

<div x-data="{ showTooltip: false }" class="relative">
    <button type="button"
            class="cursor-pointer block relative w-full"
            @click="triggerSearchShortcut"
            @mouseenter="showTooltip = true"
            @mouseleave="showTooltip = false"
    >
        {{-- Icon --}}
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <x-svg.search-classic class="w-5 h-5 text-gray-400 dark:text-gray-500" />
        </span>

        {{-- Text --}}
        @if($expanded)
            <span class="p-2.5 pl-10 {{ $spanClass }}">
                Rechercher...
            </span>

            <span class="absolute inset-y-0 right-0 flex items-center pr-2 max-lg:hidden">
                <x-app.shortcut key="⌘ K"/>
            </span>
        @else
            <span class="p-2.5 pl-2.5 {{ $spanClass }}"></span>

            {{-- Tooltip --}}
            <div x-cloak x-show="showTooltip">
                <x-tooltip
                    text="Rechercher"
                    position="right"
                    show="true"
                    colorStyle="gray"
                />
            </div>
        @endif
    </button>
</div>

<script>
    function triggerSearchShortcut() {
        setTimeout(() => {
            document.dispatchEvent(new KeyboardEvent('keydown', {
                key: 'k',
                metaKey: true,
                bubbles: true
            }));
        }, 10);
    }
</script>
