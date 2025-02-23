<div>
    {{-- Mobile menu --}}
    <div x-data="{ mobileMenuOpen: false }" class="lg:hidden">

        {{-- nav menu mobile --}}
        <div class="relative w-full bg-white dark:bg-gray-800 pl-5 pr-3.5 py-4 h-15 z-50 flex justify-between items-center">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-start" title="Retour à l'accueil"
               wire:navigate>
                <x-app-logo class="w-6 h-6"/>
                <span class="ml-4 text-xl font-bold text-gray-900 dark:text-white">FamilyNest</span>
            </a>

            <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-md cursor-pointer">
                <x-svg.menu-animate />
            </button>
        </div>

        <x-divider/>

        <!-- Overlay et contenu du menu mobile -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-full"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-full"
             class="fixed top-15 right-0 h-screen w-screen bg-white dark:bg-gray-800 z-40 overflow-y-auto"
             style="display: none;"
        >
            <x-divider class="z-50"/>

            <!-- Contenu du menu mobile -->
            <div class="flex flex-col gap-4 px-4 pt-5 pb-4 h-[calc(100%-4rem)] min-h-175">

                <!-- Search bar => spotlight -->
                <button type="button" class="cursor-pointer block relative w-full" @click="document.dispatchEvent(new KeyboardEvent('keydown', { key: 'k', metaKey: true }))">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 dark:text-gray-500"/>
                    </span>
                    <span
                        class="p-2.5 pl-10 block w-full h-10 text-sm text-gray-500 rounded-lg bg-gray-50 border border-gray-300 placeholder-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 text-left">Rechercher...</span>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 max-sm:hidden">
                        <span
                            class="bg-gray-100 text-gray-500 rounded-md px-2 py-1 text-xs font-medium dark:bg-gray-700 dark:text-gray-400">
                            ⌘K
                        </span>
                    </span>
                </button>

                <!-- Navigation links -->
                <ul class="font-medium">
                    <li>
                        <x-sidebar-menu.nav-link route="dashboard" name="Tableau de bord" menuVariable="mobileMenuOpen">
                            <x-heroicon-o-home class="text-gray-500 dark:text-gray-400"/>
                        </x-sidebar-menu.nav-link>
                    </li>
                    <li x-data="{ dropdownOpen: false }">
                        <button
                            @click="dropdownOpen = !dropdownOpen"
                            :aria-expanded="dropdownOpen"
                            type="button"
                            class="flex items-center w-full overflow-hidden transition-all px-3 py-2 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group"
                        >
                            <span
                                class="w-4.5 h-4.5 stroke-2 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
                                <x-heroicon-o-document class="text-gray-500 dark:text-gray-400"/>
                            </span>
                            <span
                                class="ml-2.5 text-gray-700 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white link-name">
                                Factures
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 :class="{'rotate-90': dropdownOpen, 'rotate-0': !dropdownOpen}"
                                 class="w-4 h-4 ml-auto transition-transform duration-200 text-gray-500 dark:text-gray-400">
                                <path d="m9 18 6-6-6-6"></path>
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
                                    <a href="#"
                                       class="flex h-8 -translate-x-px items-center gap-2 px-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 text-gray-700 dark:text-gray-400">
                                        <span>Payées</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#"
                                       class="flex h-8 -translate-x-px items-center gap-2 px-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 text-gray-700 dark:text-gray-400">
                                        <span>Impayées</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#"
                                       class="flex h-8 -translate-x-px items-center gap-2 px-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 text-gray-700 dark:text-gray-400">
                                        <span>Favorites</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <x-sidebar-menu.nav-link route="dashboard" name="Thèmes" menuVariable="mobileMenuOpen">
                            <x-heroicon-o-swatch class="text-gray-500 dark:text-gray-400"/>
                        </x-sidebar-menu.nav-link>
                    </li>
                    <li>
                        <x-sidebar-menu.nav-link route="dashboard" name="Calendrier" menuVariable="mobileMenuOpen">
                            <x-heroicon-o-calendar class="text-gray-500 dark:text-gray-400"/>
                        </x-sidebar-menu.nav-link>
                    </li>
                    <li>
                        <x-sidebar-menu.nav-link route="dashboard" name="Archives" menuVariable="mobileMenuOpen">
                            <x-heroicon-o-archive-box class="text-gray-500 dark:text-gray-400"/>
                        </x-sidebar-menu.nav-link>
                    </li>
                    <li>
                        <x-sidebar-menu.nav-link route="dashboard" name="Objectifs" menuVariable="mobileMenuOpen">
                            <x-heroicon-o-tag class="text-gray-500 dark:text-gray-400"/>
                        </x-sidebar-menu.nav-link>
                    </li>
                    <li>
                        <x-sidebar-menu.nav-link route="dashboard" name="Familles" menuVariable="mobileMenuOpen">
                            <x-heroicon-o-users class="text-gray-500 dark:text-gray-400"/>
                        </x-sidebar-menu.nav-link>
                    </li>
                </ul>

                <x-divider/>

                <!-- Facture link -->
                <a href="{{ route('invoices.create') }}" class="w-full font-semibold rounded-lg text-sm p-2.5 text-center flex items-center justify-center gap-3 text-white bg-purple-500 hover:bg-purple-600 dark:bg-gray-600 dark:hover:bg-gray-700" wire:navigate>
                    <x-heroicon-o-plus-circle class="w-5 h-5"/>
                    <span  class="text-sm-medium">Ajouter une facture</span>
                </a>

                <!-- Other nav links -->
                <ul class="font-medium custom-mt-auto">
                    <li>
                        <a href="#"
                           class="flex items-center p-2 py-1.5 text-gray-700 rounded-lg dark:text-gray-400 group">
                            <x-heroicon-o-arrow-path
                                class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"/>
                            <span class="ml-3">Rafraîchir</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                           class="flex items-center p-2 py-1.5 text-gray-700 rounded-lg dark:text-gray-400 group">
                            <x-heroicon-o-cog
                                class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"/>
                            <span class="ml-3">Paramètres</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                           class="flex items-center p-2 py-1.5 text-gray-700 rounded-lg dark:text-gray-400 group">
                            <x-heroicon-o-question-mark-circle
                                class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"/>
                            <span class="ml-3">Centre d'aide</span>
                        </a>
                    </li>
                </ul>

                <x-divider/>

                <!-- User profile dropdown -->
                <x-menu>
                    <x-menu.button class="flex w-full items-center gap-3 overflow-hidden rounded-md px-1 cursor-pointer">
                        <span class="relative flex shrink-0 overflow-hidden h-8 w-8 rounded-lg">
                            <img class="object-cover w-full h-full" src="https://avatars.githubusercontent.com/u/7221389?v=4" alt="Renaud Vmb">
                        </span>
                        <div class="grid flex-1 text-left text-sm leading-tight">
                            <span class="truncate text-sm font-semibold text-gray-900 dark:text-white">Renaud Vmb</span>
                            <span class="truncate text-xs text-gray-500 dark:text-gray-400">renaud.vanmeerbergen@gmail.com</span>
                        </div>

                        <x-arrown-direction direction="down"/>
                    </x-menu.button>

                    <x-menu.items class="w-full min-w-50 max-w-86 -mt-8 shadow-lg">
                        <x-menu.item class="block px-4 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            <x-svg.user/>
                            {{ __('Voir mon profil') }}
                        </x-menu.item>

                        <x-menu.item class="block px-4 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            <x-svg.lightning/>
                            {{ __('Raccourcis clavier') }}
                        </x-menu.item>

                        <x-divider />

                        <x-menu.item
                            class="block px-4 py-2 text-left text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            <x-svg.user-plus/>
                            {{ __('Inviter des membres') }}
                        </x-menu.item>

                        <x-menu.item class="block px-4 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            <x-svg.changelog/>
                            {{ __('Mise à jour') }}
                        </x-menu.item>

                        <x-menu.item class="block px-4 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            <x-svg.help/>
                            {{ __('Question au support') }}
                        </x-menu.item>

                        <x-divider />

                        <x-menu.item class="block px-4 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            <x-svg.trash/>
                            {{ __('Corbeille') }}
                        </x-menu.item>

                        <x-menu.item class="block px-4 py-2 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            <x-svg.logout/>
                            {{ __('Se déconnecter') }}
                        </x-menu.item>
                    </x-menu.items>
                </x-menu>
            </div>
        </div>
    </div>

    {{-- Desktop sidebar --}}
    <div x-data="{ sidebarOpen: true }"
         :class="sidebarOpen ? 'lg:w-64' : 'lg:w-20'"
         class="fixed left-0 top-0 h-full rounded-tr-[16px] rounded-br-[16px] bg-white dark:bg-gray-800 transition-all duration-400 z-50 hidden lg:block"
    >
        <div class="h-full px-4.5 pt-6 pb-4 flex flex-col justify-between gap-4 overflow-y-visible rounded-tr-[16px] rounded-br-[16px] bg-white dark:bg-gray-800">

            <!-- Header (logo & button) -->
            <div class="flex items-center justify-between pl-2">
                <a href="{{ route('dashboard') }}" class="flex items-center" title="Retour à l'accueil" wire:navigate>
                    <x-app-logo class="w-6 h-6"/>
                    <span x-show="sidebarOpen"
                          class="ml-4 text-xl font-bold text-gray-900 dark:text-white">FamilyNest</span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" type="button">
                    <div x-show="sidebarOpen"
                         class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 hover:cursor-pointer">
                        <svg width="20" height="16" class="w-5 h-5 transition-transform duration-300"
                             viewBox="0 0 20 16"
                             fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M16.593 8.00004H7.22583M7.22583 8.00004L11.9094 12.6667M7.22583 8.00004L11.9094 3.33337"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                class="stroke-gray-500 dark:stroke-gray-400"/>
                            <rect x="0.869629" width="1.00362" height="16" fill="currentColor"
                                  class="fill-gray-500 dark:fill-gray-400"/>
                        </svg>
                    </div>

                    <div x-show="!sidebarOpen" class="p-2 rounded-lg text-gray-500 hover:cursor-pointer">
                        <svg width="20" height="16" class="ml-6 w-5 h-5 transition-transform duration-300"
                             viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M6.34525 8.00004H15.7124M15.7124 8.00004L11.0288 12.6667M15.7124 8.00004L11.0288 3.33337"
                                stroke="#374054" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                class="stroke-gray-500 dark:stroke-gray-400"/>
                            <rect width="1.00362" height="16" transform="matrix(-1 0 0 1 1.06885 0)"
                                  class="fill-gray-500 dark:fill-gray-400"/>
                        </svg>
                    </div>
                </button>
            </div>

            <!-- Search bar => spotlight -->
            <button type="button" class="my-1 cursor-pointer block relative" @click="document.dispatchEvent(new KeyboardEvent('keydown', { key: 'k', metaKey: true }))">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 dark:text-gray-500"/>
                </span>
                <span :class="sidebarOpen ? 'p-2.5 pl-10' : 'p-2.5 pl-2.5'"
                      class="block w-full h-10 text-sm text-gray-500 rounded-lg bg-gray-50 border border-gray-300 placeholder-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 text-left"
                      x-text="sidebarOpen ? 'Rechercher...' : ''"></span>
                <span x-show="sidebarOpen" class="absolute inset-y-0 right-0 flex items-center pr-2">
                    <span
                        class="bg-gray-100 text-gray-500 rounded-md px-2 py-1 text-xs font-medium dark:bg-gray-700 dark:text-gray-400">
                        ⌘K
                    </span>
                </span>
            </button>

            <!-- Navigation links -->
            <ul class="font-medium">
                <li>
                    <x-sidebar-menu.nav-link route="dashboard" name="Tableau de bord" menuVariable="sidebarOpen">
                        <x-heroicon-o-home class="text-gray-500 dark:text-gray-400"/>
                    </x-sidebar-menu.nav-link>
                </li>
                <li x-data="{ dropdownOpen: false }">
                    <button
                        @click="dropdownOpen = !dropdownOpen"
                        :aria-expanded="dropdownOpen"
                        type="button"
                        class="flex items-center w-full overflow-hidden transition-all px-3 py-2 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group"
                    >
                        <span
                            class="w-4.5 h-4.5 stroke-2 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
                            <x-heroicon-o-document class="text-gray-500 dark:text-gray-400"/>
                        </span>
                        <span x-show="sidebarOpen"
                              class="ml-2.5 text-gray-700 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white link-name">
                                Factures
                            </span>
                        <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             :class="{'rotate-90': dropdownOpen, 'rotate-0': !dropdownOpen}"
                             class="w-4 h-4 ml-auto transition-transform duration-200 text-gray-500 dark:text-gray-400">
                            <path d="m9 18 6-6-6-6"></path>
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
                                <a href="#"
                                   class="flex h-8 -translate-x-px items-center gap-2 px-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 text-gray-700 dark:text-gray-400">
                                    <span>Payées</span>
                                </a>
                            </li>
                            <li>
                                <a href="#"
                                   class="flex h-8 -translate-x-px items-center gap-2 px-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 text-gray-700 dark:text-gray-400">
                                    <span>Impayées</span>
                                </a>
                            </li>
                            <li>
                                <a href="#"
                                   class="flex h-8 -translate-x-px items-center gap-2 px-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 text-gray-700 dark:text-gray-400">
                                    <span>Favorites</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <x-sidebar-menu.nav-link route="dashboard" name="Thèmes" menuVariable="sidebarOpen">
                        <x-heroicon-o-swatch class="text-gray-500 dark:text-gray-400"/>
                    </x-sidebar-menu.nav-link>
                </li>
                <li>
                    <x-sidebar-menu.nav-link route="dashboard" name="Calendrier" menuVariable="sidebarOpen">
                        <x-heroicon-o-calendar class="text-gray-500 dark:text-gray-400"/>
                    </x-sidebar-menu.nav-link>
                </li>
                <li>
                    <x-sidebar-menu.nav-link route="dashboard" name="Archives" menuVariable="sidebarOpen">
                        <x-heroicon-o-archive-box
                            class="text-gray-500 dark:text-gray-400"/>
                    </x-sidebar-menu.nav-link>
                </li>
                <li>
                    <x-sidebar-menu.nav-link route="dashboard" name="Objectifs" menuVariable="sidebarOpen">
                        <x-heroicon-o-tag class="text-gray-500 dark:text-gray-400"/>
                    </x-sidebar-menu.nav-link>
                </li>
                <li>
                    <x-sidebar-menu.nav-link route="dashboard" name="Familles" menuVariable="sidebarOpen">
                        <x-heroicon-o-users class="text-gray-500 dark:text-gray-400"/>
                    </x-sidebar-menu.nav-link>
                </li>
            </ul>

            {{-- Divider --}}
            <x-divider/>

            <!-- Facture link -->
            <a href="{{ route('invoices.create') }}" class="w-full font-semibold rounded-lg text-sm p-2.5 text-center flex items-center justify-center gap-3 text-white bg-purple-500 hover:bg-purple-600 dark:bg-gray-600 dark:hover:bg-gray-700" wire:navigate>
                <x-heroicon-o-plus-circle class="w-5 h-5"/>
                <span x-show="sidebarOpen" class="text-sm-medium">Ajouter une facture</span>
            </a>

            <!-- Other nav links -->
            <ul class="font-medium mt-auto">
                <li>
                    <a href="#" class="flex items-center p-2 py-1.5 text-gray-700 rounded-lg dark:text-gray-400 group">
                        <x-heroicon-o-arrow-path
                            class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"/>
                        <span x-show="sidebarOpen" class="ml-3">Rafraîchir</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-2 py-1.5 text-gray-700 rounded-lg dark:text-gray-400 group">
                        <x-heroicon-o-cog
                            class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"/>
                        <span x-show="sidebarOpen" class="ml-3">Paramètres</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-2 py-1.5 text-gray-700 rounded-lg dark:text-gray-400 group">
                        <x-heroicon-o-question-mark-circle
                            class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"/>
                        <span x-show="sidebarOpen" class="ml-3">Centre d'aide</span>
                    </a>
                </li>
            </ul>

            {{-- Divider --}}
            <x-divider/>

            <!-- User profile dropdown -->
            <x-menu>
                <x-menu.button class="flex w-full items-center gap-3 overflow-hidden rounded-md px-1 cursor-pointer">
                        <span class="relative flex shrink-0 overflow-hidden h-8 w-8 rounded-lg">
                            <img class="object-cover w-full h-full"
                                 src="https://avatars.githubusercontent.com/u/7221389?v=4" alt="Renaud Vmb">
                        </span>
                    <div class="grid flex-1 text-left text-sm leading-tight">
                        <span class="truncate text-sm font-semibold text-gray-900 dark:text-white">Renaud Vmb</span>
                        <span class="truncate text-xs text-gray-500 dark:text-gray-400">renaud.vanmeerbergen@gmail.com</span>
                    </div>

                    <x-arrown-direction direction="down"/>
                </x-menu.button>

                <x-menu.items class="w-full min-w-50 max-w-55 -mt-6.5 shadow-lg">
                    <x-menu.item class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        <x-svg.user/>
                        {{ __('Voir mon profil') }}
                    </x-menu.item>

                    <x-menu.item class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        <x-svg.lightning/>
                        {{ __('Raccourcis clavier') }}
                    </x-menu.item>

                    <x-divider />

                    <x-menu.item class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        <x-svg.user-plus/>
                        {{ __('Inviter des membres') }}
                    </x-menu.item>

                    <x-menu.item class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        <x-svg.changelog/>
                        {{ __('Mise à jour') }}
                    </x-menu.item>

                    <x-menu.item class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        <x-svg.help/>
                        {{ __('Question au support') }}
                    </x-menu.item>

                    <x-divider />

                    <x-menu.item class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        <x-svg.trash/>
                        {{ __('Corbeille') }}
                    </x-menu.item>

                    <x-menu.item class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        <x-svg.logout/>
                        {{ __('Se déconnecter') }}
                    </x-menu.item>
                </x-menu.items>
            </x-menu>
        </div>
    </div>
</div>
