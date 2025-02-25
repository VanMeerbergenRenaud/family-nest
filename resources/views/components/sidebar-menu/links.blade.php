@props([
    'sidebarWide' => null,
    'menuVariable' => 'mobileMenuOpen',
])

<ul class="font-medium">
    <li>
        <x-sidebar-menu.link route="dashboard" name="Tableau de bord" menuVariable="{{ $menuVariable }}">
            <x-heroicon-o-home class="text-gray-500 dark:text-gray-400"/>
        </x-sidebar-menu.link>
    </li>

    @if($sidebarWide === 'false')
        <li>
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center px-3 py-2 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group">
            <span class="w-4.5 h-4.5 stroke-2 text-[#344054] dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
                <x-heroicon-o-document class="text-gray-500 dark:text-gray-400"/>
            </span>
                <span x-show="{{ $menuVariable }}" class="ml-2.5 text-[#344054] dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white link-name">
                Factures
            </span>
            </a>
        </li>
    @else
        <li x-data="{ dropdownOpen: false }">
            <button
                type="button"
                :aria-expanded="dropdownOpen"
                @click="dropdownOpen = !dropdownOpen"
                class="flex items-center w-full overflow-hidden transition-all px-3 py-2 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group"
            >
                <span class="w-4.5 h-4.5 stroke-2 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
                    <x-heroicon-o-document class="text-gray-500 dark:text-gray-400"/>
                </span>
                <span x-show="{{ $menuVariable }}"
                      class="ml-2.5 text-gray-700 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white link-name">
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
                 class="relative left-0 my-1 w-full"
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
                        <a href="#" class="flex h-8 -translate-x-px items-center gap-2 px-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            <span>Payées</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex h-8 -translate-x-px items-center gap-2 px-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            <span>Impayées</span>
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
    <li>
        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center px-3 py-2 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group">
            <span class="w-4.5 h-4.5 stroke-2 text-[#344054] dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
                <x-heroicon-o-swatch class="text-gray-500 dark:text-gray-400"/>
            </span>
                    <span x-show="{{ $menuVariable }}" class="ml-2.5 text-[#344054] dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white link-name">
                Thèmes
            </span>
        </a>
    </li>
    <li>
        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center px-3 py-2 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group">
            <span class="w-4.5 h-4.5 stroke-2 text-[#344054] dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
                <x-heroicon-o-calendar class="text-gray-500 dark:text-gray-400"/>
            </span>
            <span x-show="{{ $menuVariable }}" class="ml-2.5 text-[#344054] dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white link-name">
                Calendrier
            </span>
        </a>
    </li>
    <li>
        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center px-3 py-2 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group">
            <span class="w-4.5 h-4.5 stroke-2 text-[#344054] dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
                <x-heroicon-o-archive-box class="text-gray-500 dark:text-gray-400"/>
            </span>
            <span x-show="{{ $menuVariable }}" class="ml-2.5 text-[#344054] dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white link-name">
                Archives
            </span>
        </a>
    </li>
    <li>
        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center px-3 py-2 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group">
            <span class="w-4.5 h-4.5 stroke-2 text-[#344054] dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
                <x-heroicon-o-tag class="text-gray-500 dark:text-gray-400"/>
            </span>
            <span x-show="{{ $menuVariable }}" class="ml-2.5 text-[#344054] dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white link-name">
                Objectifs
            </span>
        </a>
    </li>
    <li>
        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center px-3 py-2 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group">
            <span class="w-4.5 h-4.5 stroke-2 text-[#344054] dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
                <x-heroicon-o-users class="text-gray-500 dark:text-gray-400"/>
            </span>
            <span x-show="{{ $menuVariable }}" class="ml-2.5 text-[#344054] dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white link-name">
                Familles
            </span>
        </a>
    </li>
</ul>
