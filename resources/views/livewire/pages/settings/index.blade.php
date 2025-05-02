<div>
    <header class="px-4">
        <h2 role="heading" aria-level="2" class="text-xl-semibold" role="heading" aria-level="2">
            {{ __('Paramètres généraux') }}
        </h2>
        <p class="text-sm-regular text-gray-500">
            {{ __('Gérez les membres de votre famille et leurs autorisations de compte ici.') }}
        </p>
    </header>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Profil (Indigo) -->
        <a href="{{ route('settings.profile') }}"
           class="group relative flex flex-col bg-white dark:bg-gray-800 rounded-xl border border-slate-200 dark:border-gray-700 transition-all duration-300 hover:border-indigo-200 dark:hover:border-indigo-900/50 overflow-hidden"
           wire:navigate
        >
            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 ease-out transform translate-x-2 -translate-y-2 group-hover:translate-x-0 group-hover:translate-y-0">
                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800">
                    <x-svg.arrow-up-hover />
                </div>
            </div>
            <div class="px-5 py-4 group-hover:bg-gray-50/50 dark:group-hover:bg-gray-800/60 transition-colors duration-300">
                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0 p-3 h-fit bg-indigo-50 dark:bg-indigo-900/30 rounded-lg border border-indigo-100 dark:border-indigo-800/40 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-800/50 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-6 h-6 text-indigo-600 dark:text-indigo-400"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 role="heading" aria-level="3"
                            class="text-base font-semibold text-gray-800 dark:text-white transition-colors duration-300 group-hover:text-indigo-700 dark:group-hover:text-indigo-400"
                        >
                            Profil
                        </h3>
                        <p class="mt-1.5 text-sm text-gray-600 dark:text-gray-400">
                            Éditer les informations de votre profil personnel et professionnels.
                        </p>
                        <p class="mt-3 text-xs-medium flex items-center gap-2 text-indigo-600 dark:text-indigo-400">
                            <x-svg.help class="inline w-3 h-3 text-indigo-600 dark:text-indigo-400" />
                            Modifier mon mot de passe
                        </p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Stockage (Blue) -->
        <a href="{{ route('settings.storage') }}"
           class="group relative flex flex-col bg-white dark:bg-gray-800 rounded-xl border border-slate-200 dark:border-gray-700 transition-all duration-300 hover:border-sky-200 dark:hover:border-sky-900/50 overflow-hidden"
           wire:navigate
        >
            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 ease-out transform translate-x-2 -translate-y-2 group-hover:translate-x-0 group-hover:translate-y-0">
                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-sky-50 dark:bg-sky-900/30 border border-sky-100 dark:border-sky-800">
                    <x-svg.arrow-up-hover />
                </div>
            </div>
            <div class="px-5 py-4 group-hover:bg-gray-50/50 dark:group-hover:bg-gray-800/60 transition-colors duration-300">
                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0 p-3 h-fit bg-sky-50 dark:bg-sky-900/30 rounded-lg border border-sky-100 dark:border-sky-800/40 group-hover:bg-sky-100 dark:group-hover:bg-sky-800/50 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-6 h-6 text-sky-600 dark:text-sky-400"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 role="heading" aria-level="3"
                            class="text-base font-semibold text-gray-800 dark:text-white transition-colors duration-300 group-hover:text-sky-700 dark:group-hover:text-sky-400"
                        >
                            Stockage
                        </h3>
                        <p class="mt-1.5 text-sm text-gray-600 dark:text-gray-400">
                            Gérer vos fichiers et surveiller votre espace de stockage restant.
                        </p>
                        <div class="mt-3 flex items-center gap-2">
                            <div class="h-1.5 flex-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="bg-sky-500 dark:bg-sky-400 h-full rounded-full" style="width: 65%"></div>
                            </div>
                            <span class="text-xs text-gray-600 dark:text-gray-400">65%</span>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Notifications (Amber/Orange) -->
        <a href="{{ route('settings.notifications') }}"
           class="group relative flex flex-col bg-white dark:bg-gray-800 rounded-xl border border-slate-200 dark:border-gray-700 transition-all duration-300 hover:border-orange-200 dark:hover:border-orange-900/50 overflow-hidden"
           wire:navigate
        >
            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 ease-out transform translate-x-2 -translate-y-2 group-hover:translate-x-0 group-hover:translate-y-0">
                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-orange-50 dark:bg-orange-900/30 border border-orange-100 dark:border-orange-800">
                    <x-svg.arrow-up-hover />
                </div>
            </div>
            <div class="px-5 py-4 group-hover:bg-gray-50/50 dark:group-hover:bg-gray-800/60 transition-colors duration-300">
                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0 p-3 h-fit bg-orange-50 dark:bg-orange-900/30 rounded-lg border border-orange-100 dark:border-orange-800/40 group-hover:bg-orange-100 dark:group-hover:bg-orange-800/50 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-6 h-6 text-orange-600 dark:text-orange-400"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 role="heading" aria-level="3"
                            class="text-base font-semibold text-gray-800 dark:text-white transition-colors duration-300 group-hover:text-orange-700 dark:group-hover:text-orange-400"
                        >
                            Notifications
                        </h3>
                        <p class="mt-1.5 text-sm text-gray-600 dark:text-gray-400">
                            Personnaliser les alertes email, push et in-app de votre compte.
                        </p>
                        <div class="mt-3 flex items-center gap-1.5">
                                <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/50">
                                    <span class="h-1.5 w-1.5 rounded-full bg-orange-500 dark:bg-orange-400"></span>
                                </span>
                            <span class="text-xs text-orange-600 dark:text-orange-400">4 notifications non lues</span>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Apparence (Purple) -->
        <a href="{{ route('settings.appearance') }}"
           class="group relative flex flex-col bg-white dark:bg-gray-800 rounded-xl border border-slate-200 dark:border-gray-700 transition-all duration-300 hover:border-purple-200 dark:hover:border-purple-900/50 overflow-hidden"
           wire:navigate>
            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 ease-out transform translate-x-2 -translate-y-2 group-hover:translate-x-0 group-hover:translate-y-0">
                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-purple-50 dark:bg-purple-900/30 border border-purple-100 dark:border-purple-800">
                    <x-svg.arrow-up-hover />
                </div>
            </div>
            <div class="px-5 py-4 group-hover:bg-gray-50/50 dark:group-hover:bg-gray-800/60 transition-colors duration-300">
                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0 p-3 h-fit bg-purple-50 dark:bg-purple-900/30 rounded-lg border border-purple-100 dark:border-purple-800/40 group-hover:bg-purple-100 dark:group-hover:bg-purple-800/50 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-6 h-6 text-purple-600 dark:text-purple-400"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 role="heading" aria-level="3"
                            class="text-base font-semibold text-gray-800 dark:text-white transition-colors duration-300 group-hover:text-purple-700 dark:group-hover:text-purple-400"
                        >
                            Apparence
                        </h3>
                        <p class="mt-1.5 text-sm text-gray-600 dark:text-gray-400">
                            Personnaliser les thèmes, couleurs et mise en page de l'interface.
                        </p>
                        <div class="mt-3 flex items-center gap-1">
                            <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/50">
                                <span class="h-1.5 w-1.5 rounded-full bg-purple-500 dark:bg-purple-400"></span>
                            </span>
                            <span class="text-xs text-purple-600 dark:text-purple-400">Thème sombre activé</span>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Plan de paiement (Teal - remplace Emerald) -->
        <a href="{{ route('settings.billing') }}"
           class="group relative flex flex-col bg-white dark:bg-gray-800 rounded-xl border border-slate-200 dark:border-gray-700 transition-all duration-300 hover:border-teal-300 dark:hover:border-teal-900/50 overflow-hidden"
           wire:navigate>
            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 ease-out transform translate-x-2 -translate-y-2 group-hover:translate-x-0 group-hover:translate-y-0">
                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-teal-50 dark:bg-teal-900/30 border border-teal-100 dark:border-teal-800">
                    <x-svg.arrow-up-hover />
                </div>
            </div>
            <div class="px-5 py-4 group-hover:bg-gray-50/50 dark:group-hover:bg-gray-800/60 transition-colors duration-300">
                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0 p-3 h-fit bg-teal-50 dark:bg-teal-900/30 rounded-lg border border-teal-100 dark:border-teal-800/40 group-hover:bg-teal-100 dark:group-hover:bg-teal-800/50 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-6 h-6 text-teal-600 dark:text-teal-400"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 role="heading" aria-level="3"
                            class="text-base font-semibold text-gray-800 dark:text-white transition-colors duration-300 group-hover:text-teal-700 dark:group-hover:text-teal-400"
                        >
                            Plan de paiement
                        </h3>
                        <p class="mt-1.5 text-sm text-gray-600 dark:text-gray-400">
                            Gérer votre abonnement, facturation et historique de paiement.
                        </p>
                        <div class="mt-3 inline-block px-2 py-1 bg-teal-50 dark:bg-teal-900/30 rounded text-xs font-medium text-teal-700 dark:text-teal-400">
                            Plan Premium · Prochain paiement 15/04
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Zone de danger (Red) -->
        <a href="{{ route('settings.danger') }}"
           class="group relative flex flex-col bg-white dark:bg-gray-800 rounded-xl border border-red-100 dark:border-red-900/40 transition-all duration-300 hover:border-red-200 dark:hover:border-red-800 overflow-hidden"
           wire:navigate>
            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 ease-out transform translate-x-2 -translate-y-2 group-hover:translate-x-0 group-hover:translate-y-0">
                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-800">
                    <x-svg.arrow-up-hover />
                </div>
            </div>
            <div class="px-5 py-4 group-hover:bg-gray-50/50 dark:group-hover:bg-gray-800/60 transition-colors duration-300">
                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0 p-3 h-fit bg-red-50 dark:bg-red-900/30 rounded-lg border border-red-100 dark:border-red-800/40 group-hover:bg-red-100 dark:group-hover:bg-red-800/50 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-6 h-6 text-red-600 dark:text-red-400"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 role="heading" aria-level="3"
                            class="text-base font-semibold text-gray-800 dark:text-white transition-colors duration-300 group-hover:text-red-700 dark:group-hover:text-red-400"
                        >
                            Zone de danger
                        </h3>
                        <p class="mt-1.5 text-sm text-gray-600 dark:text-gray-400">
                            Supprimer des données ou désactiver définitivement votre compte.
                        </p>
                        <div class="mt-3 text-xs text-red-600 dark:text-red-400 font-medium">
                            Actions irréversibles
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
