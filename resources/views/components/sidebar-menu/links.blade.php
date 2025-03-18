@props([
    'expanded' => true,
])

<ul>
    <x-sidebar-menu.link href="{{ route('dashboard') }}" icon="home" label="Tableau de bord" :expanded="$expanded" />
    <x-sidebar-menu.link href="{{ route('invoices') }}" icon="document" label="Factures" :expanded="$expanded" />
    <x-sidebar-menu.link href="{{ route('themes') }}" icon="swatch" label="Thèmes" :expanded="$expanded" />
    <x-sidebar-menu.link href="{{ route('calendar') }}" icon="calendar" label="Calendrier" :expanded="$expanded" />
    <x-sidebar-menu.link href="{{ route('goals') }}" icon="tag" label="Objectifs" :expanded="$expanded" />
    <x-sidebar-menu.link href="{{ route('family') }}" icon="users" label="Familles" :expanded="$expanded" />

    {{-- Dropdown style --}}
    {{--<div x-show="dropdownOpen"
         x-collapse
         class="relative left-0 mt-1 mb-2 w-full"
         style="display: none;"
         x-transition:enter="transition-opacity duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
    >
        <ul class="ml-5 flex translate-x-px flex-col gap-1 border-l border-gray-200 dark:border-gray-700 px-2.5 py-0.5">
            <li>
                <a href="{{ route('invoices.create') }}" class="flex h-8 -translate-x-px items-center gap-2 px-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <span>Payées</span>
                </a>
            </li>
            <li>
                <a href="{{ route('invoices') }}" class="flex h-8 -translate-x-px items-center gap-2 px-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <span>Toutes</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex h-8 -translate-x-px items-center gap-2 px-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <span>Favorites</span>
                </a>
            </li>
        </ul>
    </div>--}}
</ul>
