<div>
    <x-header title="Apparence"
              description=" Modifiez l'apparence et la convivialité de votre tableau de bord."
              class="mb-5"
    />

    <div class="px-2 space-y-4 max-w-4xl">

        <!-- Logo app -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-slate-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 dark:border-gray-700">
                <h3 role="heading" aria-level="3" class="text-sm-medium text-gray-900 dark:text-white">Logo de l'entreprise</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Mettez à jour le logo de votre entreprise affiché dans votre application.
                </p>
            </div>
            <div class="p-5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 bg-gray-100 border border-slate-200 rounded-lg p-3 h-14 w-14 flex-center">
                        <x-app-logo class="h-8 w-8 text-white" />
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Format recommandé :</p>
                        <p class="text-xs font-medium text-gray-800 dark:text-gray-200">SVG, PNG (min. 512x512px)</p>
                    </div>
                </div>
                <button type="button" class="button-primary">
                    Changer le logo
                </button>
            </div>
        </div>

        <!-- Preferences -->
        <div class="bg-white dark:bg-gray-900 rounded-lg border border-slate-200 dark:border-gray-800 overflow-hidden">
            <div class="px-6 py-4">
                <h3 role="heading" aria-level="3" class="text-sm-medium text-gray-900 dark:text-white">Préférence d'affichage</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Choisissez le mode d'affichage qui vous convient le mieux
                </p>
            </div>
            <div class="px-4 pb-4">
                <div class="grid grid-cols-3 gap-4 mt-2">
                    <!-- Option Clair -->
                    <div class="appearance-option">
                        <button class="w-full bg-gray-50 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 border border-slate-200 dark:border-gray-700 rounded-lg p-4 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-600 group relative overflow-hidden">
                            <div class="flex flex-col items-center">
                                <div class="p-2 mb-3 rounded-full bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="5"></circle>
                                        <line x1="12" y1="1" x2="12" y2="3"></line>
                                        <line x1="12" y1="21" x2="12" y2="23"></line>
                                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                        <line x1="1" y1="12" x2="3" y2="12"></line>
                                        <line x1="21" y1="12" x2="23" y2="12"></line>
                                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Clair</span>
                            </div>
                        </button>
                    </div>

                    <!-- Option Sombre -->
                    <div class="appearance-option">
                        <button class="w-full bg-gray-50 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 border border-slate-200 dark:border-gray-700 rounded-lg p-4 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-600 group relative overflow-hidden">
                            <div class="flex flex-col items-center">
                                <div class="p-2 mb-3 rounded-full bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Sombre</span>
                            </div>
                        </button>
                    </div>

                    <!-- Option Système -->
                    <div class="appearance-option">
                        <button class="w-full bg-gray-50 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 border border-slate-200 dark:border-gray-700 rounded-lg p-4 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-600 group relative overflow-hidden">
                            <div class="flex flex-col items-center">
                                <div class="p-2 mb-3 rounded-full bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                        <line x1="8" y1="21" x2="16" y2="21"></line>
                                        <line x1="12" y1="17" x2="12" y2="21"></line>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Système</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Languages -->
        <div class="flex items-center justify-between gap-4 px-4 py-3 bg-white dark:bg-gray-800 rounded-lg border border-slate-200 dark:border-gray-700 overflow-hidden">
            <div>
                <h3 role="heading" aria-level="3" class="text-sm-medium text-gray-900 dark:text-white">Langue</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Choisissez la langue par défaut pour l'interface de l'application.
                </p>
            </div>
            <x-form.select class="mt-2" name="language" label="" model="language">
                <option value="" disabled>Sélectionner une langue</option>
                <option value="fr" selected>Français</option>
                <option value="en">Anglais</option>
                <option value="es">Espagnol</option>
                <option value="de">Allemand</option>
            </x-form.select>
        </div>

        <!-- Reset -->
        <div class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 rounded-lg border border-slate-200 dark:border-gray-700 overflow-hidden">
            <div>
                <h3 role="heading" aria-level="3" class="text-sm-medium text-gray-900 dark:text-white">Réinitialiser les paramètres</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Restaurez tous les paramètres d'apparence à leurs valeurs par défaut.
                </p>
            </div>
            <button type="button" class="button-secondary">
                Restaurer les réglages par défaut
            </button>
        </div>
    </div>
</div>
