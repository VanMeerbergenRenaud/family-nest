@props([
    'isSidebarOpen' => false,
])

<aside
    role="complementary"
    aria-hidden="{{ $isSidebarOpen ? 'false' : 'true' }}"
    class="sidebar {{ $isSidebarOpen ? 'sidebar--open' : 'sidebar--closed' }}"
    x-on:keydown.escape.window="isOpen = false; $wire.set('isSidebarOpen', false)"
>
    <!-- Overlay -->
    <div class="sidebar__overlay" wire:click="toggleSidebar" tabindex="-1" aria-label="Fermer la sidebar"></div>

    <!-- Container -->
    <div class="sidebar__container">
        <div class="sidebar__container__content">

            <!-- Header -->
            <div class="sidebar__container__content__header">
                {{ $header }}
            </div>

            <!-- Body -->
            <div class="sidebar__container__content__body">
                {{ $content }}
            </div>

            <!-- Footer -->
            <div class="sidebar__container__content__footer">
                {{ $footer }}
            </div>

        </div>
    </div>
</aside>
