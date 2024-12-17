<div
    class="p-0 w-48 outline-none border border-gray-300 rounded-md bg-white overflow-hidden z-10"
    x-cloak
    x-menu:items
    x-transition:enter.origin.top.right
    x-anchor.bottom-start="document.getElementById($id('alpine-menu-button'))"
>
    {{ $slot }}
</div>
