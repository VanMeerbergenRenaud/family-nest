@props([
    'title' => null,
    'description' => null,
    'showSocialLogin' => false
])

<div class="lg:py-4 relative min-h-screen flex-center bg-white md:bg-gray-100">
    {{-- Arrow go back --}}
    <div class="absolute top-4 left-4 max-sm:hidden">
        <a href="{{ route('welcome') }}"
           title="Retour à l'accueil"
           wire:navigate
           class="homepage-link text-sm"
        >
            <x-svg.arrows.left class="w-3.5 h-3.5 text-gray-700" />
            {{ __('Retour') }}
        </a>
    </div>

    <div class="bg-white max-w-lg mx-auto p-1.5 md:rounded-2xl md:shadow-md">
        <div class="relative flex-center flex-col rounded-xl w-full h-fit overflow-hidden bg-gradient-to-t from-[#FFFFFF] via-[#F7F9FF] via-35% to-[#EBF1FF]">
            {{-- Background en grille --}}
            <div class="absolute pointer-events-none inset-0 bg-[url('/public/img/bg-pattern.svg')] bg-cover bg-[50%_calc(50%_-_0.75rem)] bg-no-repeat">
                <div class="absolute inset-0"></div>
            </div>

            <!-- Logo -->
            <div class="my-6 p-4 bg-blue-100 rounded-full z-2">
                <a href="{{ route('welcome') }}" title="Retour à l'accueil" wire:navigate>
                    <x-app.logo class="w-8 h-8"/>
                </a>
            </div>

            <!-- Titre & description -->
            <div class="text-center z-2 px-8">
                <h1 role="heading" aria-level="1" class="text-xl-semibold text-gray-900">
                    {{ $title }}
                </h1>
                <p class="mt-2 text-sm-regular text-gray-700">
                    {{ $description }}
                </p>
            </div>
        </div>

        <div class="container rounded-2xl py-8 px-5 md:px-7 max-w-[34rem] md:max-h-fit space-y-4 max-sm:shadow-sm">

            @if (session('google_auth_error'))
                <p class="mt-4 mb-5 bg-red-50 border border-red-200 py-2 px-4 gap-4 rounded-md text-sm text-red-700 dark:bg-red-100 dark:border-red-300 dark:text-red-600 flex items-center">
                    <x-svg.error class="h-4 w-4" />
                    {{ session('google_auth_error') }}
                </p>
            @endif

            <!-- Boutons de connexion via les réseaux sociaux -->
            @if($showSocialLogin)
                <div x-data="{ showTooltip: false }"
                     class="space-y-3 md:space-y-0 md:flex md:space-x-4 min-w-[20rem]"
                >

                    <a href="{{ route('google.redirect') }}"  class="w-full flex-center py-2.5 px-4 text-sm-semibold rounded-xl transition-colors bg-gray-100 text-dark hover:bg-gray-200">
                        <x-svg.google class="mr-3"/>
                        Continuer&nbsp;avec&nbsp;Google
                    </a>
                    {{--<a href="{{ route('apple.redirect') }}" class="cursor-not-allowed w-full flex-center py-2.5 px-4 text-sm-semibold rounded-xl transition-colors bg-gray-100 text-dark hover:bg-gray-200">
                        <x-svg.apple class="mr-3"/>
                        Continuer&nbsp;avec&nbsp;Apple
                    </a>--}}

                    <button class="relative cursor-not-allowed w-full flex-center py-2.5 px-4 text-sm-semibold rounded-xl transition-colors bg-gray-100 text-dark hover:bg-gray-200"
                            @mouseenter="showTooltip = true"
                            @mouseleave="showTooltip = false"
                    >
                        <x-svg.apple class="mr-3"/>
                        Continuer&nbsp;avec&nbsp;Apple
                        <div x-cloak x-show="showTooltip">
                            <x-tooltip
                                text="Arrive bientôt !"
                                position="top"
                                show="true"
                            />
                        </div>
                    </button>
                </div>

                <!-- Séparateur OU -->
                <div class="flex-center my-5">
                    <div class="border-b-2 border-gray-200 flex-grow rounded-md"></div>
                    <span class="mx-3.5 text-gray-500 text-xs-medium">OU</span>
                    <div class="border-b-2 border-gray-200 flex-grow rounded-md"></div>
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>
</div>
