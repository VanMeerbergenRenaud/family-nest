<div class="flex flex-col">
    {{-- Mobile sidebar menu --}}
    <aside x-data="{ mobileMenuOpen: false }"
           class="lg:hidden"
    >
        <h2 role="heading" aria-level="2" class="sr-only">Menu de navigation principal</h2>

        {{-- Divider with space --}}
        <div class="relative h-15"></div>

        {{-- Navigation header --}}
        <div class="fixed top-0 left-0 w-full pl-5 pr-3.5 py-4 h-15 z-50 flex justify-between items-center bg-white dark:bg-gray-800">
            <a href="{{ route('dashboard') }}"
               title="Retour à l'accueil"
               class="flex items-center justify-start"
               wire:navigate>
                <x-app.logo class="w-6 h-6"/>
                <span class="ml-4 text-xl-bold text-gray-900 dark:text-white">
                    FamilyNest
                </span>
            </a>

            <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-md cursor-pointer">
                <x-svg.menu-animate/>
            </button>
        </div>

        <x-divider/>

        <!-- Sidebar -->
        <div x-cloak
             x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-full"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-full"
             class="fixed inset-0 top-15 h-screen w-screen bg-white dark:bg-gray-800 z-40 overflow-y-auto"
        >
            <x-divider/>

            <!-- Content -->
            <div class="flex flex-col gap-4 px-4 pt-5 pb-4 h-[calc(100%-4rem)] min-h-175">

                <!-- Search bar => spotlight -->
                <x-sidebar-menu.search />

                <!-- Principal links -->
                <x-sidebar-menu.links menu-variable="mobileMenuOpen"/>

                <x-divider/>

                <!-- New invoice link -->
                <x-sidebar-menu.add-invoice />

                <!-- Bottom links -->
                <x-sidebar-menu.bottom-links />

                <x-divider/>

                <!-- User profile dropdown -->
                <x-sidebar-menu.user-dropdown :$user :$showShortcutsModal />
            </div>
        </div>
    </aside>

    {{-- Desktop sidebar menu --}}
    <aside
        class="hidden lg:flex lg:flex-col lg:fixed h-full rounded-tr-[16px] rounded-br-[16px] bg-white dark:bg-gray-800 transition-all duration-300 z-50"
        style="width: {{ session('sidebar_expanded', true) ? '16rem' : '5rem' }};"
    >
        <div class="h-full px-4.5 pt-6 pb-4 flex flex-col justify-between gap-4 overflow-y-visible rounded-tr-[16px] rounded-br-[16px] bg-white dark:bg-gray-800">

        <!-- Navigation header -->
            <div x-data="{ showTooltip: false }" class="relative flex items-center justify-between pl-2.5">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center"
                   title="Retour à l'accueil"
                   @mouseenter="showTooltip = true"
                   @mouseleave="showTooltip = false"
                   wire:navigate
                >
                    <x-app.logo class="w-6 h-6"/>
                    @if($expanded)
                        <span class="ml-4 text-xl-bold text-gray-900 dark:text-white">FamilyNest</span>
                    @else
                        <div x-cloak x-show="showTooltip">
                            <x-tooltip
                                text="Accueil"
                                position="right"
                                show="true"
                                colorStyle="white"
                            />
                        </div>
                    @endif
                </a>
                @if($expanded)
                    <button type="button"
                            aria-label="Toggle Sidebar"
                            wire:click="toggleSidebar"
                            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        <svg width="20" height="16" class="w-5 h-5 transition-transform duration-300"
                             viewBox="0 0 20 16"
                             fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16.593 8.00004H7.22583M7.22583 8.00004L11.9094 12.6667M7.22583 8.00004L11.9094 3.33337"
                                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                  stroke-linejoin="round"
                                  class="stroke-gray-500 dark:stroke-gray-400"/>
                            <rect x="0.869629" width="1.00362" height="16" fill="currentColor"
                                  class="fill-gray-500 dark:fill-gray-400"/>
                        </svg>
                    </button>
                @else
                    <button type="button"
                            aria-label="Toggle Sidebar"
                            wire:click="toggleSidebar"
                            class="ml-6 p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        <svg width="20" height="16" class="w-5 h-5 transition-transform duration-300"
                             viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.34525 8.00004H15.7124M15.7124 8.00004L11.0288 12.6667M15.7124 8.00004L11.0288 3.33337"
                                  stroke="#374054" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                  class="stroke-gray-500 dark:stroke-gray-400"/>
                            <rect width="1.00362" height="16" transform="matrix(-1 0 0 1 1.06885 0)"
                                  class="fill-gray-500 dark:fill-gray-400"/>
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Search bar => spotlight -->
            <x-sidebar-menu.search :expanded="$expanded"/>

            <!-- Principal links -->
            <x-sidebar-menu.links :expanded="$expanded"/>

            <x-divider/>

            <!-- New invoice link -->
            <x-sidebar-menu.add-invoice :expanded="$expanded"/>

            <!-- Bottom links -->
            <x-sidebar-menu.bottom-links :expanded="$expanded"/>

            <x-divider/>

            <!-- User profile dropdown -->
            <x-sidebar-menu.user-dropdown :$user :expanded="$expanded" :$showShortcutsModal />
        </div>
    </aside>

    @if($showShortcutsModal)
        <x-modal wire:model="showShortcutsModal">
            <x-modal.panel>
                 <div class="p-5 rounded-lg bg-gray-50">
                        <h2 class="text-lg font-medium text-gray-900 mb-5 flex items-center gap-2.5">
                            {{ __('Raccourcis clavier') }}
                            <span class="mt-0.75">⌨️</span>
                        </h2>

                        <ul>
                            <li class="px-1 flex items-center justify-between py-3">
                                <span class="text-sm-medium text-gray-700">Ouvrir la barre de recherche</span>
                                <x-app.shortcut key="⌘ + K" />
                            </li>
                            <x-divider />
                            <li class="px-1 flex items-center justify-between py-3">
                                <span class="text-sm-medium text-gray-700">Créer une nouvelle facture</span>
                                <x-app.shortcut key="⌘ + X" />
                            </li>
                        </ul>
                    </div>

                <x-modal.footer>
                    <x-modal.close>
                        <button type="button" class="button-secondary">
                            {{ __('Fermer') }}
                        </button>
                    </x-modal.close>
                </x-modal.footer>
            </x-modal.panel>
        </x-modal>
    @endif
</div>
