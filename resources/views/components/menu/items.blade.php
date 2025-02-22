<div
    x-cloak
    x-menu:items
    x-transition:enter.origin.top.right
    x-anchor.bottom-start="document.getElementById($id('alpine-menu-button'))"
    {{ $attributes->merge(['class' => 'p-0 min-w-36 overflow-hidden z-10 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg']) }}
>
    {{ $slot }}
</div>
