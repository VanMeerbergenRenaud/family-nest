@props([
    'isSidebarOpen' => false,
])

<aside
    role="complementary"
    aria-hidden="{{ $isSidebarOpen ? 'false' : 'true' }}"
    class="fixed inset-0 z-10 transition-opacity duration-300 ease-out
    {{ $isSidebarOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none' }}"
    x-on:keydown.escape.window="isOpen = false;
    $wire.set('isSidebarOpen', false)"
>
    <!-- Overlay -->
    <div class="absolute inset-0 w-full h-full bg-black/10"
         wire:click="toggleSidebar"
         tabindex="-1"
         aria-label="Fermer la sidebar"></div>

    <!-- Container -->
    <div class="absolute min-w-full top-10 right-0 bottom-0 md:min-w-[25vw] max-w-[65vw] bg-white overflow-y-auto scrollbar-none rounded-2.5 transition-transform duration-500 ease-out rounded-[0.65rem] md:top-2.5 md:right-2.5 md:bottom-2.5 md:left-auto left-0
        {{ $isSidebarOpen ? 'translate-y-0 md:translate-x-0' : 'translate-y-full md:translate-y-0 md:translate-x-full' }}"
    >
        <div class="relative h-full min-h-[calc(100vh-1.5rem)] bg-white overflow-y-auto scrollbar-thin">

            <!-- Header -->
            <div class="sticky top-0 border-b p-4 pt-6 bg-white border-gray-200">
                {{ $header }}
            </div>

            <!-- Body -->
            <div class="pt-4 px-4 pb-20">
                {{ $content }}
            </div>

            <!-- Footer -->
            <div class="fixed inset-x-0 bottom-0 p-3 bg-white border-t border-gray-200">
                {{ $footer }}
            </div>
        </div>
    </div>
</aside>
