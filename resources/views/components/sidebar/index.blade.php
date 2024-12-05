@props(['isSidebarOpen' => false, 'title' => 'Titre de la sidebar'])

<aside class="sidebar {{ $isSidebarOpen ? 'sidebar--open' : 'sidebar--closed' }}">
    <div class="sidebar__overlay" wire:click="toggleSidebar" tabindex="-1"></div>
    <div class="sidebar__container" role="dialog" aria-modal="true" aria-label="{{ $title }}">
        <div class="sidebar__container__content">
            <div class="sidebar__container__content__header">
                <button wire:click="$set('isSidebarOpen', false)" class="close">
                    <x-svg.cross/>
                </button>

                <h2 class="title">{{ $title }}</h2>
            </div>

            <div class="sidebar__container__content__body">
                {{ $content }}
            </div>
        </div>
    </div>
</aside>
