@props([
    'footer' => null,
    'isSidebarOpen' => false,
])

<aside
    role="complementary"
    aria-hidden="{{ $isSidebarOpen ? 'false' : 'true' }}"
    class="fixed inset-0 z-70 {{ $isSidebarOpen ? 'block' : 'hidden' }}"
    @keydown.escape.window="$wire.toggleSidebar()"
    wire:transition
    {{ $attributes }}
>
        <!-- Overlay -->
        <div class="absolute inset-0 w-full h-full bg-black/10 transition-opacity backdrop-blur-[1px] duration-500 ease-in-out"
             wire:click="toggleSidebar"
             tabindex="-1"
             aria-label="Fermer la sidebar"></div>

        <!-- Container -->
        <div class="absolute min-w-full top-0 right-0 bottom-0 md:min-w-[25vw] max-w-[40vw] bg-white overflow-y-auto scrollbar-none rounded-2.5 rounded-[0.65rem] md:top-2.5 md:right-2.5 md:bottom-2.5 md:left-auto left-0
            {{ $isSidebarOpen ? 'translate-y-0 md:translate-x-0' : 'translate-y-full md:translate-y-0 md:translate-x-full' }}"
        >
            <div class="relative h-full min-h-[calc(100vh-1.5rem)] bg-white overflow-y-auto scrollbar-thin">

                <!-- Header -->
                <div class="sticky top-0 border-b p-4 pr-12 bg-white border-gray-200">
                    {{ $header }}

                    <div class="absolute top-0 right-0 p-3 z-10">
                        <button type="button" wire:click="toggleSidebar" class="p-2.5 rounded-full flex--center text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600">
                            <x-svg.cross/>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="pt-4 px-4 @if($footer) pb-20 @endif">
                    {{ $content }}
                </div>

                <!-- Footer -->
                @if($footer)
                    <div class="fixed inset-x-0 bottom-0 p-3 bg-white border-t border-gray-200">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        </div>
    </aside>
