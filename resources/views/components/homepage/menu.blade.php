<header
    x-data="{ isMobileMenuOpen: false }"
    @keydown.escape.window="isMobileMenuOpen = false"
    class="fixed top-0 inset-x-0 z-10"
>
    <!-- ======================================================= -->
    <!-- Barre de Navigation pour DESKTOP -->
    <!-- ======================================================= -->
    <nav class="hidden lg:block px-4 lg:px-12 bg-gray-50 border-b border-slate-200">
        <div class="relative flex items-center justify-between">
            <!-- Section gauche: logo et liens de navigation -->
            <div class="flex items-center gap-x-12">
                <a href="/" aria-label="Page d'accueil" class="py-4 group flex items-center" title="Page d'accueil">
                    <x-app.logo class="h-6.5 w-6.5 group-hover:scale-102"/>
                    <span class="ml-3 text-xl-semibold text-slate-900 group-hover:scale-102">
                        FamilyNest
                    </span>
                </a>

                <!-- Liens -->
                <div x-data="{ showTooltip: false }" class="py-4 flex items-center gap-2">
                    <a href="{{ route('about') }}"
                       title="Vers la page À propos"
                       class="homepage-link {{ request()->routeIs('about') ? 'bg-slate-200/50' : '' }}" wire:navigate
                    >
                        À propos
                    </a>
                    <a href="{{ route('tutorials') }}"
                       title="Vers la page Tutoriels"
                       class="homepage-link {{ request()->routeIs('tutorials') ? 'bg-slate-200/50' : '' }}" wire:navigate
                    >
                        Tutoriels
                    </a>
                    <a href="{{ route('changelog') }}"
                       title="Vers la page Nouveautés"
                       class="homepage-link {{ request()->routeIs('changelog') ? 'bg-slate-200/50' : '' }}" wire:navigate
                    >
                        Nouveautés
                    </a>
                    {{--<a href="{{ route('pricing') }}"
                       class="homepage-link {{ request()->routeIs('pricing') ? 'bg-slate-200/50' : '' }}" wire:navigate
                    >
                        Prix
                    </a>--}}
                    <button
                        class="homepage-link w-fit relative cursor-not-allowed pr-2 group hover:bg-gray-100"
                        @mouseenter="showTooltip = true"
                        @mouseleave="showTooltip = false"
                    >
                        <span class="group-hover:opacity-50 font-medium text-[#2E3238] group-hover:text-slate-900 transition-all duration-300">
                            Prix
                            <span x-cloak x-show="showTooltip" class="text-xs-medium">(arrive bientôt)</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Section droite -->
            <div class="py-4 flex items-center gap-3">
                <a href="{{ route('register') }}" wire:navigate class="button-primary">
                    Créer un compte
                </a>
                <a href="{{ route('login') }}" wire:navigate class="button-secondary">
                    Se connecter
                </a>
            </div>
        </div>
    </nav>

    <!-- ======================================================= -->
    <!-- Barre de Navigation et Menu pour MOBILE -->
    <!-- ======================================================= -->
    <div class="lg:hidden">
        <!-- Fond cliquable pour fermer -->
        <div x-show="isMobileMenuOpen" x-cloak
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40" @click="isMobileMenuOpen = false"
        ></div>

        <!-- Le conteneur unique qui s'anime -->
        <div
            class="fixed top-4 left-4 right-4 z-50 rounded-xl bg-slate-100/80 text-slate-900 ring-1 ring-slate-900/5 backdrop-blur-sm overflow-hidden transition-[max-height] duration-300 ease-out"
            :class="{ 'max-h-15': !isMobileMenuOpen, 'max-h-[85vh]': isMobileMenuOpen }"
        >
            <!-- Navbar -->
            <div class="flex h-15 items-center justify-between px-4">
                <a href="/" aria-label="Page d'accueil" class="flex items-center">
                    <x-app.logo class="w-6 h-6"/>
                    <span class="ml-2.5 text-md-semibold">FamilyNest</span>
                </a>
                <button @click="isMobileMenuOpen = !isMobileMenuOpen" type="button" class="-m-2.5 p-2.5">
                    <span class="sr-only">Ouvrir/Fermer le menu</span>
                    <svg x-show="!isMobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                    <svg x-show="isMobileMenuOpen" x-cloak fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         class="h-6 w-6 transition-transform duration-200"
                         :class="isMobileMenuOpen ? 'rotate-180' : ''"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Menu -->
            <div class="p-4 pt-0">
                <div class="border-t border-slate-200 pt-4">
                    <div class="space-y-1">
                        <a href="/"
                           class="block rounded-lg px-3 py-2 text-md-medium leading-6 hover:bg-slate-200/75 {{ request()->is('/') ? 'bg-slate-200' : '' }}"
                           wire:navigate
                        >
                            Accueil
                        </a>
                        <a href="{{ route('about') }}"
                           class="block rounded-lg px-3 py-2 text-md-medium leading-6 hover:bg-slate-200/75 {{ request()->routeIs('about') ? 'bg-slate-200' : '' }}"
                           wire:navigate
                        >
                            À propos
                        </a>
                        <a href="{{ route('tutorials') }}"
                           class="block rounded-lg px-3 py-2 text-md-medium leading-6 hover:bg-slate-200/75 {{ request()->routeIs('tutorials') ? 'bg-slate-200' : '' }}"
                           wire:navigate
                        >
                            Tutoriels
                        </a>
                        <a href="{{ route('changelog') }}"
                           class="block rounded-lg px-3 py-2 text-md-medium leading-6 hover:bg-slate-200/75 {{ request()->routeIs('changelog') ? 'bg-slate-200' : '' }}"
                           wire:navigate
                        >
                            Nouveautés
                        </a>
                        <span class="cursor-not-allowed block rounded-lg px-3 py-2 text-md-medium leading-6 hover:bg-slate-200/75 {{ request()->routeIs('features') ? 'bg-slate-200' : '' }}">
                            Prix <span class="ml-0.5 text-sm-medium">(arrive bientôt)</span>
                        </span>
                    </div>
                    <div class="border-t border-slate-200 mt-4 pt-4">
                        <a href="{{ route('register') }}"
                           class="block rounded-lg px-3 py-2 text-md-medium leading-6 hover:bg-slate-200/75 {{ request()->routeIs('register') ? 'bg-slate-200' : '' }}"
                           wire:navigate
                        >
                            Créer un compte
                        </a>
                        <a href="{{ route('login') }}"
                           class="block rounded-lg px-3 py-2 text-md-medium leading-6 hover:bg-slate-200/75 {{ request()->routeIs('login') ? 'bg-slate-200' : '' }}"
                           wire:navigate
                        >
                            Se connecter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
