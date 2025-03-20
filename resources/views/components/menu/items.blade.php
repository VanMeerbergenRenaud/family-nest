<div
    x-cloak
    x-menu:items
    x-transition:enter.origin.top.right
    x-anchor.bottom-start="document.getElementById($id('alpine-menu-button'))"
    {{ $attributes->merge(['class' => 'mt-2 overflow-hidden rounded-lg shadow-sm z-10 min-w-48 p-[.3125rem] border border-zinc-200 dark:border-zinc-600 bg-white dark:bg-zinc-700']) }}
>
    {{ $slot }}
</div>
