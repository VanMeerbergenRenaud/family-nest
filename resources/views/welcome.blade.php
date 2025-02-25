<x-guest-layout>
    <div class="bg-white min-h-screen flex flex-col">
        <div class="py-8">
            <div class="container mx-auto px-4 flex justify-between items-center">
                <a href="{{ route('welcome') }}" class="flex items-center space-x-4" title="FamilyNest">
                    <x-app-logo class="w-6 h-auto" />
                    <h1 role="heading" aria-level="1" class="text-2xl font-bold tracking-tight text-gray-900">
                        FamilyNest
                    </h1>
                </a>
                <nav class="hidden md:flex space-x-8">
                    <a href="#" class="text-base font-medium text-gray-500 hover:text-gray-900">Fonctionnalités</a>
                    <a href="#" class="text-base font-medium text-gray-500 hover:text-gray-900">Tarifs</a>
                    <a href="#" class="text-base font-medium text-gray-500 hover:text-gray-900">Tutoriels</a>
                </nav>
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-base font-medium text-gray-500 hover:text-gray-900">Se connecter</a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        S’inscrire
                    </a>
                </div>
                <div class="md:hidden">
                    <button type="button" class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-expanded="false">
                        <span class="sr-only">Ouvrir le menu principal</span>
                        <!-- Heroicon name: outline/menu -->
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex-grow">
            <div class="container mx-auto px-4 sm:px-6 py-16 md:py-26">
                <div class="lg:grid lg:grid-cols-2 lg:gap-16 lg:items-center">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 sm:text-5xl">
                            Simplifiez la gestion des finances familiales
                        </h2>
                        <p class="mt-6 text-gray-700 text-lg pr-12">
                            FamilyNest vous aide à organiser facilement les factures, suivre les dépenses, et collaborer avec votre famille pour une meilleure gestion financière.
                        </p>
                        <ul role="list" class="mt-8 space-y-4">
                            <li class="flex items-center">
                                <svg class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="ml-3 text-gray-700">Gestion centralisée des factures</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="ml-3 text-gray-700">Suivi précis des dépenses par catégorie</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="ml-3 text-gray-700">Collaboration familiale simplifiée</span>
                            </li>
                        </ul>
                        <div class="mt-10 flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
                            <a href="{{ route('register') }}"
                               class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors duration-200"> {{-- Ajout de `justify-center` et transition --}}
                                S'inscrire gratuitement
                            </a>
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-colors duration-200"> {{-- Ajout de `justify-center` et transition --}}
                                Se connecter
                            </a>
                        </div>
                    </div>
                    <div class="mt-8 lg:mt-0">
                        <img src="{{ asset('img/mockup.png') }}"
                             alt="Illustration de gestion des finances familiales"
                             class="cursor-pointer w-full rounded-lg shadow-lg scale-110 transition-transform duration-300 ease-out hover:shadow-xl hover:scale-115"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 py-5">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-500">
                <p>
                    © {{ date('Y') }} FamilyNest - <a href="https://renaud-vmb.com" class="text-blue-dark custom-underline-link">Renaud Vmb</a>. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
