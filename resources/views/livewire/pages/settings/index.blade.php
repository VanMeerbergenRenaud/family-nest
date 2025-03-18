<div>
        <header>
            <h2 class="display-xs-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Paramètres généraux') }}
            </h2>
            <p class="mt-1 text-md-regular text-gray-600 dark:text-gray-400">
                {{ __('Modifier les informations que vous souhaitez ci-dessous') }}
            </p>
        </header>

    <div class="py-8 max-w-4xl">
        <div class="space-y-6">
            <!-- Première rangée -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

                <!-- Profil -->
                <a href="{{ route('settings.profile') }}" class="flex flex-col bg-white rounded-lg border border-slate-200" wire:navigate>
                    <div class="p-4">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 p-2.5 h-fit bg-gray-100 rounded-lg border border-slate-200 dark:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-md-medium text-gray-900 dark:text-white">Profil</h3>
                                <p class="mt-0.5 text-sm-regular text-gray-600 dark:text-gray-400">Éditer les informations de votre profil</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Stockage -->
                <a href="{{ route('settings.storage') }}" class="flex flex-col bg-white rounded-lg border border-slate-200" wire:navigate>
                    <div class="p-4">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 p-2.5 h-fit bg-gray-100 rounded-lg border border-slate-200 dark:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-md-medium text-gray-900 dark:text-white">Stockage</h3>
                                <p class="mt-0 text-sm-regular text-gray-600 dark:text-gray-400">Regarder les détails de votre stockage utilisé</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Notifications -->
                <a href="{{ route('settings.notifications') }}" class="flex flex-col bg-white rounded-lg border border-slate-200" wire:navigate>
                    <div class="p-4">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 p-2.5 h-fit bg-gray-100 rounded-lg border border-slate-200 dark:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-md-medium text-gray-900 dark:text-white">Notifications</h3>
                                <p class="mt-0.5 text-sm-regular text-gray-600 dark:text-gray-400">Changer les paramètres des notifications</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Deuxième rangée -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Plan de paiement -->
                <a href="{{ route('settings.billing') }}" class="flex flex-col bg-white rounded-lg border border-slate-200" wire:navigate>
                    <div class="p-4">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 p-2.5 h-fit bg-gray-100 rounded-lg border border-slate-200 dark:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-md-medium text-gray-900 dark:text-white">Plan de paiement</h3>
                                <p class="mt-0.5 text-sm-regular text-gray-600 dark:text-gray-400">Changer de plan avec toutes les fonctionnalités</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Apparence -->
                <a href="{{ route('settings.appearance') }}" class="flex flex-col bg-white rounded-lg border border-slate-200" wire:navigate>
                    <div class="p-4">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 p-2.5 h-fit bg-gray-100 rounded-lg border border-slate-200 dark:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-md-medium text-gray-900 dark:text-white">Apparence</h3>
                                <p class="mt-0.5 text-sm-regular text-gray-600 dark:text-gray-400">Éditer l'apparence de l'application</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Zone de danger -->
                <a href="{{ route('settings.danger') }}" class="flex flex-col bg-white rounded-lg border border-slate-200" wire:navigate>
                    <div class="p-4">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 p-2.5 h-fit bg-gray-100 rounded-lg border border-slate-200 dark:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-md-medium text-gray-900 dark:text-white">Zone de danger</h3>
                                <p class="mt-0.5 text-sm-regular text-gray-600 dark:text-gray-400">Tout supprimer du compte.</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
