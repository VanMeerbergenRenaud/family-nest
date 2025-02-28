@props([
    'sidebarWide' => null,
    'menuVariable' => 'desktopMenuOpen',
])

<ul>
    <x-sidebar-menu.link href="{{ route('dashboard') }}" icon="home" label="Tableau de bord" :menu-variable="$menuVariable" />

    @if($sidebarWide === 'false')
        <x-sidebar-menu.link href="{{ route('invoices') }}" icon="document" label="Factures" :menu-variable="$menuVariable" />
    @else
        <li x-data="{ dropdownOpen: false }" class="rounded-lg overflow-visible basic-transition text-gray-700 bg-white dark:text-gray-400 dark:bg-gray-800">
            <button
                type="button"
                :aria-expanded="dropdownOpen"
                @click="dropdownOpen = !dropdownOpen"
                class="group flex items-center w-full px-3 py-2 h-10 rounded-lg text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 group">
                <span class="w-4.5 h-4.5 stroke-2 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                    <x-heroicon-o-document />
                </span>
                    <span x-show="{{ $menuVariable }}" class="ml-2.5 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                    Factures
                </span>
                <svg x-show="{{ $menuVariable }}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     :class="{'rotate-90': dropdownOpen, 'rotate-0': !dropdownOpen}"
                     class="w-4 h-4 ml-auto transition-transform duration-200 text-gray-500 dark:text-gray-400">
                    <path d="m9 18 6-6-6-6" />
                </svg>
            </button>

            <div x-show="dropdownOpen"
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
                <ul class="ml-5 flex translate-x-px flex-col gap-1 border-l border-gray-200 dark:border-gray-700 px-2.5 py-0.5 group-data-[collapsible=icon]:hidden">
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
            </div>
        </li>
    @endif

    <x-sidebar-menu.link href="{{ route('invoices') }}" icon="swatch" label="Thèmes" :menu-variable="$menuVariable" />
    <x-sidebar-menu.link href="{{ route('invoices') }}" icon="calendar" label="Calendrier" :menu-variable="$menuVariable" />
    <x-sidebar-menu.link href="{{ route('invoices') }}" icon="archive-box" label="Archives" :menu-variable="$menuVariable" />
    <x-sidebar-menu.link href="{{ route('invoices') }}" icon="tag" label="Objectifs" :menu-variable="$menuVariable" />
    <x-sidebar-menu.link href="{{ route('invoices') }}" icon="users" label="Familles" :menu-variable="$menuVariable" />
</ul>
