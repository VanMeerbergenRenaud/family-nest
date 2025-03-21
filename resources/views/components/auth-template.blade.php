@props([
    'title' => null,
    'description' => null,
    'showSocialLogin' => false
])

<div class="relative min-h-screen flex-center bg-gray-100">
    {{-- Arrow go back --}}
    <div class="absolute top-4 left-4 max-sm:hidden">
        <a href="{{ route('welcome') }}"
           title="Retour à l'accueil"
           wire:navigate
           class="button-classic"
        >
            <x-svg.arrows.left class="w-4 h-4 text-gray-700" />
            Retour
        </a>
    </div>

    <div class="bg-white p-1.5 rounded-2xl shadow-md">
        <div class="relative flex-center flex-col rounded-xl w-full h-fit overflow-hidden bg-gradient-to-t from-[#FFFFFF] via-[#F7F9FF] via-35% to-[#EBF1FF]">
            {{-- Background en grille --}}
            <div class="absolute pointer-events-none inset-0 bg-[url('/public/img/bg-pattern.svg')] bg-cover bg-[50%_calc(50%_-_0.75rem)] bg-no-repeat">
                <div class="absolute inset-0"></div>
            </div>

            <!-- Logo -->
            <div class="my-6 p-4 bg-blue-100 rounded-full z-2">
                <a href="{{ route('welcome') }}" title="Retour à l'accueil" wire:navigate>
                    <x-app-logo class="w-8 h-8"/>
                </a>
            </div>

            <!-- Titre & description -->
            <div class="text-center z-2 px-8">
                <h2 class="text-xl-semibold text-gray-900">
                    {{ $title }}
                </h2>
                <p class="mt-2 text-sm-regular text-gray-700">
                    {{ $description }}
                </p>
            </div>
        </div>

        <div class="container rounded-2xl py-8 px-5 md:px-7 max-sm:max-w-[25rem] max-w-[32rem] md:max-h-fit space-y-4">

            <!-- Boutons de connexion via les réseaux sociaux -->
            @if($showSocialLogin)
                <div class="space-y-3 md:space-y-0 md:flex md:space-x-4">
                    <button class="w-full flex-center py-2.5 px-4 text-sm-semibold rounded-xl transition-colors bg-gray-100 text-dark hover:bg-gray-200">
                        <x-svg.google class="mr-2.5"/>
                        Continuer&nbsp;avec&nbsp;Google
                    </button>
                    <button class="w-full flex-center py-3 px-4 text-sm-semibold rounded-xl transition-colors bg-gray-100 text-dark hover:bg-gray-200">
                        <x-svg.apple class="mr-2.5"/>
                        Continuer&nbsp;avec&nbsp;Apple
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
