<x-guest-layout>
    <h1 role="heading" aria-level="1" class="sr-only">Bienvenue sur FamilyNest</h1>

    <!-- Hero Section avec éléments flottants -->
    <div class="relative min-h-screen w-full overflow-hidden bg-white"
         x-data="{
         showOnboarding: false,
         showMembers: false,
         showStats: false,
         showTasks: false,
         showProgress: false
     }"
         x-init="
         setTimeout(() => showOnboarding = true, 200);
         setTimeout(() => showMembers = true, 400);
         setTimeout(() => showStats = true, 600);
         setTimeout(() => showTasks = true, 800);
         setTimeout(() => showProgress = true, 1000);
     ">

        <!-- Élément d'onboarding flottant -->
        <div class="max-lg:hidden absolute left-0 top-1/4 -translate-x-1/2 -rotate-6 transform transition-all duration-700 ease-out"
             :class="showOnboarding ? 'translate-x-32' : ''">
            <div class="rounded-xl bg-white p-4 shadow-lg">
                <div class="flex items-center space-x-2">
                    <div class="h-3 w-3 rounded-full bg-[#e15b64]"></div>
                    <p class="font-medium text-gray-800">Simplifiez vos finances</p>
                </div>
                <div class="mt-3 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Factures en attente</span>
                        <span class="text-sm font-semibold text-gray-800">5</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Factures archivées</span>
                        <span class="text-sm font-semibold text-gray-800">28</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Économies réalisées</span>
                        <span class="text-sm font-semibold text-[#3b4d89]">215€</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques flottantes -->
        <div class="max-lg:hidden absolute left-10 top-1/2 -translate-y-1/2 rotate-3 transform transition-all duration-700 ease-out"
             :class="showStats ? 'opacity-100' : 'opacity-0 translate-y-10'">
            <div class="space-y-4 rounded-lg bg-white/80 p-5 shadow-lg backdrop-blur-sm">
                <div class="space-y-1">
                    <div class="text-xs font-light text-gray-500">Maîtrise du budget</div>
                    <div class="font-medium text-gray-900">68% de visibilité en plus</div>
                </div>
                <div class="space-y-1">
                    <div class="text-xs font-light text-gray-500">Économies mensuelles</div>
                    <div class="font-medium text-gray-900">45€ en moyenne</div>
                </div>
                <div class="space-y-1">
                    <div class="text-xs font-light text-gray-500">Factures organisées</div>
                    <div class="font-medium text-gray-900">96% automatisé</div>
                </div>
            </div>
        </div>

        <!-- Tâches flottantes -->
        <div class="max-lg:hidden absolute right-10 top-1/3 translate-y-0 rotate-6 transform transition-all duration-700 ease-out"
             :class="showTasks ? 'opacity-100' : 'opacity-0 translate-y-10'">
            <div class="w-64 rounded-xl bg-white p-4 shadow-lg">
                <div class="flex items-center space-x-2">
                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-[#88c0d5]/20">
                        <div class="h-2 w-2 rounded-full bg-[#88c0d5]"></div>
                    </div>
                    <p class="font-medium text-gray-800">Factures récentes</p>
                </div>
                <div class="mt-3 space-y-2">
                    <div class="flex items-center space-x-3">
                        <div class="flex h-4 w-4 items-center justify-center rounded-full border border-[#3b4d89]/30">
                            <div class="h-2 w-2 rounded-full bg-[#3b4d89]/30"></div>
                        </div>
                        <span class="text-sm text-gray-600">Électricité - 94€</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex h-4 w-4 items-center justify-center rounded-full border border-[#e15b64]/30">
                            <div class="h-2 w-2 rounded-full bg-[#e15b64]"></div>
                        </div>
                        <span class="text-sm text-gray-600">Internet - 39€</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex h-4 w-4 items-center justify-center rounded-full border border-[#3b4d89]/30">
                            <div class="h-2 w-2 rounded-full bg-[#3b4d89]/30"></div>
                        </div>
                        <span class="text-sm text-gray-600">Assurance - 68€</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique flottant -->
        <div class="max-lg:hidden absolute bottom-1/4 right-8 translate-y-16 -rotate-3 transform transition-all duration-700 ease-out md:right-32"
             :class="showProgress ? 'translate-y-0 opacity-100' : 'opacity-0'">
            <div class="rounded-xl bg-white p-4 shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-gray-500">Évolution des dépenses</span>
                    <span class="text-xs font-medium text-green-500">-12%</span>
                </div>
                <div class="mt-2 h-16 w-48">
                    <div class="relative h-full w-full">
                        <!-- Ligne de tendance -->
                        <svg class="h-full w-full" viewBox="0 0 100 40">
                            <path d="M0,35 Q20,38 25,25 T40,15 T60,20 T75,5 T100,15" fill="none" stroke="#3b4d89" stroke-width="2"/>
                            <circle cx="25" cy="25" r="1.5" fill="#3b4d89"/>
                            <circle cx="40" cy="15" r="1.5" fill="#3b4d89"/>
                            <circle cx="60" cy="20" r="1.5" fill="#3b4d89"/>
                            <circle cx="75" cy="5" r="1.5" fill="#3b4d89"/>
                            <circle cx="100" cy="15" r="1.5" fill="#e15b64"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Membres flottants -->
        <div class="max-lg:hidden absolute bottom-20 left-20 -rotate-6 transform transition-all duration-700 ease-out"
             :class="showMembers ? 'opacity-100' : 'opacity-0 translate-y-10'">
            <div class="rounded-xl bg-white p-4 shadow-lg">
                <div class="flex items-center space-x-2">
                    <div class="flex -space-x-2">
                        <div class="h-6 w-6 rounded-full bg-[#e15b64] ring-2 ring-white"></div>
                        <div class="h-6 w-6 rounded-full bg-[#3b4d89] ring-2 ring-white"></div>
                        <div class="h-6 w-6 rounded-full bg-[#88c0d5] ring-2 ring-white"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Rejoignez 5k+ familles</span>
                    <div class="rounded-full bg-[#3b4d89]/10 px-2 py-1">
                        <span class="text-xs font-medium text-[#3b4d89]">→</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu central hero -->
        <div class="relative mx-auto flex min-h-screen max-w-7xl flex-col items-center justify-center px-4 pt-16 sm:px-6 lg:px-8">
            <a href="/" class="relative -top-10 p-4 rounded-full shadow-xs bg-white/80 backdrop-blur-sm transition-all duration-700 ease-out hover:scale-105">
                <x-app.logo class="h-9 w-9 text-white cursor-pointer" />
            </a>

            <div class="max-w-3xl text-center">
                <h2 role="heading" aria-level="2" class="mb-8 text-4xl font-medium text-gray-900 sm:text-5xl md:text-6xl">
                    Centralisez et organisez vos factures familiales
                </h2>
                <p class="mb-10 mx-auto max-w-2xl text-[1.125rem] text-gray-500">
                    Dans un monde digital, gérez toutes vos factures efficacement au même endroit.
                    Visualisez vos dépenses, évitez les imprévus et collaborez avec votre famille pour une meilleure maîtrise financière.
                </p>
                <div class="flex flex-col items-center justify-center space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
                    <a href="{{ route('register') }}"
                       wire:navigate
                       class="inline-flex items-center rounded-full bg-[#3b4d89] px-8 py-3 text-base font-medium text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-[#9370db]">
                        Créer un compte
                    </a>
                    <a href="{{ route('login') }}"
                       wire:navigate
                       class="inline-flex items-center rounded-full border border-gray-300 bg-white px-8 py-3 text-base font-medium text-gray-700 shadow-sm transition hover:-translate-y-0.5 hover:border-gray-300 hover:text-[#3b4d89]"
                    >
                        Se connecter
                    </a>
                </div>
            </div>
        </div>

        <!-- Arrière-plan cercles avec variations d'opacité -->
        <div class="absolute left-1/4 top-1/4 -z-10 h-64 w-64 rounded-full bg-[#e15b64]/5"></div>
        <div class="absolute bottom-1/4 right-1/3 -z-10 h-96 w-96 rounded-full bg-[#3b4d89]/5"></div>
        <div class="absolute bottom-1/3 left-1/2 -z-10 h-48 w-48 rounded-full bg-[#88c0d5]/5"></div>
    </div>

    <p class="py-4 text-center text-sm text-gray-500">
        © {{ date('Y') }} FamilyNest - Développé par <a href="https://renaud-vmb.com" class="text-sm  text-[#3b4d89] hover:text-[#9370db] transition duration-200" target="_blank" rel="noopener noreferrer"
            > Renaud Vmb </a>. Tous droits réservés.
    </p>
</x-guest-layout>
