<button
    type="button"
    x-menu:button
    {{ $attributes }}
    class="w-full rounded-none"
    x-bind:class="{ 'bg-gray-100 dark:bg-gray-700': menuOpen }"
>
    {{ $slot }}
</button>
