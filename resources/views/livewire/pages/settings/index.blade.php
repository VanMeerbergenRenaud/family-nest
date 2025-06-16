<div>
    <header class="px-2 md:px-4">
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
           class="group relative flex flex-col bg-white rounded-xl border border-slate-200 transition-all duration-300 overflow-hidden"
           wire:navigate
        >
            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 ease-out transform translate-x-2 -translate-y-2 group-hover:translate-x-0 group-hover:translate-y-0">
                <div class="flex-center h-8 w-8 rounded-full bg-gray-100">
                    <x-svg.arrow-up-hover />
                </div>
            </div>
            <div class="px-5 py-4 group-hover:bg-gray-50/75 transition-colors duration-300 h-full">
                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0 p-3 h-fit bg-gray-100 rounded-lg group-hover:bg-cyan-200/50 transition-colors duration-300">
                        <x-svg.user class="w-6 h-6 text-cyan-600" />
                    </div>
                    <div class="flex-1">
                        <h3 role="heading" aria-level="3" class="text-md-semibold text-gray-800 transition-colors duration-300 group-hover:text-cyan-700">
                            Profil
                        </h3>
                        <p class="mt-1.5 text-sm text-gray-600">
                            Éditer les informations de votre profil personnel et professionnels.
                        </p>
                        <p class="mt-3 text-xs-medium flex items-center gap-2 text-cyan-600">
                            <x-svg.help class="inline w-3 h-3 text-cyan-600" />
                            Modifier mon mot de passe
                        </p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Stockage (Blue) -->
        <a href="{{ route('settings.storage') }}"
           class="group relative flex flex-col bg-white rounded-xl border border-slate-200 transition-all duration-300 overflow-hidden"
           wire:navigate
        >
            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 ease-out transform translate-x-2 -translate-y-2 group-hover:translate-x-0 group-hover:translate-y-0">
                <div class="flex-center h-8 w-8 rounded-full bg-gray-100">
                    <x-svg.arrow-up-hover />
                </div>
            </div>
            <div class="px-5 py-4 group-hover:bg-gray-50/75 transition-colors duration-300 h-full">
                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0 p-3 h-fit bg-gray-100 rounded-lg group-hover:bg-sky-100 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 role="heading" aria-level="3" class="text-md-semibold text-gray-800 transition-colors duration-300 group-hover:text-sky-700">
                            Stockage
                        </h3>
                        <p class="mt-1.5 text-sm text-gray-600">
                            Gérer vos fichiers et surveiller votre espace de stockage restant.
                        </p>
                        <div class="mt-3 flex items-center gap-2">
                            <div class="h-1.5 flex-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="bg-sky-500 h-full rounded-full" style="width: 0"></div>
                            </div>
                            <span class="text-xs text-gray-600">0%</span>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Notifications (Amber/Orange) -->
        <a href="{{ route('settings.notifications') }}"
           class="group relative flex flex-col bg-white rounded-xl border border-slate-200 transition-all duration-300 overflow-hidden"
           wire:navigate
        >
            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 ease-out transform translate-x-2 -translate-y-2 group-hover:translate-x-0 group-hover:translate-y-0">
                <div class="flex-center h-8 w-8 rounded-full bg-gray-100">
                    <x-svg.arrow-up-hover />
                </div>
            </div>
            <div class="px-5 py-4 group-hover:bg-gray-50/75 transition-colors duration-300 h-full">
                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0 p-3 h-fit bg-gray-100 rounded-lg group-hover:bg-orange-100 transition-colors duration-300">
                        <x-svg.bell class="w-6 h-6 text-orange-600" />
                    </div>
                    <div class="flex-1">
                        <h3 role="heading" aria-level="3" class="text-md-semibold text-gray-800 transition-colors duration-300 group-hover:text-orange-700">
                            Notifications
                        </h3>
                        <p class="mt-1.5 text-sm text-gray-600">
                            Personnaliser les alertes email, push et in-app de votre compte.
                        </p>
                        <div class="mt-3 flex items-center gap-1.5">
                            <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-orange-100">
                                <span class="h-1.5 w-1.5 rounded-full bg-orange-500"></span>
                            </span>
                            <span class="text-xs text-orange-600">Notifications non lues</span>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Apparence (Purple) -->
        <a href="{{ route('settings.appearance') }}"
           class="group relative flex flex-col bg-white rounded-xl border border-slate-200 transition-all duration-300 overflow-hidden"
           wire:navigate
        >
            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 ease-out transform translate-x-2 -translate-y-2 group-hover:translate-x-0 group-hover:translate-y-0">
                <div class="flex-center h-8 w-8 rounded-full bg-gray-100">
                    <x-svg.arrow-up-hover />
                </div>
            </div>
            <div class="px-5 py-4 group-hover:bg-gray-50/75 transition-colors duration-300 h-full">
                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0 p-3 h-fit bg-gray-100 rounded-lg group-hover:bg-fuchsia-100 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-500/90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 role="heading" aria-level="3" class="text-md-semibold text-gray-800 transition-colors duration-300 group-hover:text-purple-700">
                            Apparence
                        </h3>
                        <p class="mt-1.5 text-sm text-gray-600">
                            Personnaliser les thèmes, couleurs et mise en page de l'interface.
                        </p>
                        <div class="mt-3 flex items-center gap-1">
                            <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-fuchsia-100">
                                <span class="h-1.5 w-1.5 rounded-full bg-fuchsia-500"></span>
                            </span>
                            <span class="text-xs text-purple-600">
                                Customisation du thème
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Plan de paiement (Teal - remplace Emerald) -->
        <a href="{{ route('settings.billing') }}"
           class="group relative flex flex-col bg-white rounded-xl border border-slate-200 transition-all duration-300 overflow-hidden"
           wire:navigate
        >
            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 ease-out transform translate-x-2 -translate-y-2 group-hover:translate-x-0 group-hover:translate-y-0">
                <div class="flex-center h-8 w-8 rounded-full bg-gray-100">
                    <x-svg.arrow-up-hover />
                </div>
            </div>
            <div class="px-5 py-4 group-hover:bg-gray-50/75 transition-colors duration-300 h-full">
                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0 p-3 h-fit bg-gray-100 rounded-lg group-hover:bg-teal-100 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 role="heading" aria-level="3" class="text-md-semibold text-gray-800 transition-colors duration-300 group-hover:text-teal-700">
                            Plan de paiement
                        </h3>
                        <p class="mt-1.5 text-sm text-gray-600">
                            Gérer votre abonnement, facturation et historique de paiement.
                        </p>
                        <div class="mt-3 inline-block px-2 py-1 bg-teal-50 rounded text-xs-medium text-teal-700">
                            Plan basique · Essai gratuit
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <!-- Zone de danger (Red) -->
        <a href="{{ route('settings.danger') }}"
           class="group relative flex flex-col bg-white rounded-xl border border-red-100 transition-all duration-300 overflow-hidden"
           wire:navigate
        >
            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 ease-out transform translate-x-2 -translate-y-2 group-hover:translate-x-0 group-hover:translate-y-0">
                <div class="flex-center h-8 w-8 rounded-full bg-gray-100">
                    <x-svg.arrow-up-hover />
                </div>
            </div>
            <div class="px-5 py-4 group-hover:bg-gray-50/75 transition-colors duration-300 h-full">
                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0 p-3 h-fit bg-gray-100 rounded-lg group-hover:bg-red-100 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 role="heading" aria-level="3" class="text-md-semibold text-gray-800 transition-colors duration-300 group-hover:text-red-700">
                            Zone de danger
                        </h3>
                        <p class="mt-1.5 text-sm text-gray-600">
                            Supprimer des données ou désactiver définitivement votre compte.
                        </p>
                        <div class="mt-3 text-xs text-red-600 font-medium">
                            Actions irréversibles
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
