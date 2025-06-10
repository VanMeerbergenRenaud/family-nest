@props([
    'step',
    'title',
    'description',
    'totalSteps' => 4,
    'layout' => 'full'
])

<div class="bg-white rounded-2xl border border-slate-200 max-w-4xl w-full grid {{ $layout === 'split' ? 'md:grid-cols-2 lg:grid-cols-7 lg:p-6' : 'lg:px-4' }} min-h-[62.5vh] min-w-[60vw] overflow-hidden">

    {{-- Colonne de gauche (ou pleine largeur) pour le contenu --}}
    <div class="p-6 flex flex-col gap-6 h-full {{ $layout === 'split' ? 'lg:col-span-3' : 'lg:col-span-7' }}">

        {{-- En-tête de l'étape --}}
        <div class="mt-4 flex flex-col space-y-3">
            <span class="text-sm font-semibold uppercase tracking-wider text-indigo-600">
                Étape {{ $step }} sur {{ $totalSteps }}
            </span>
            <h2 role="heading" aria-level="2" class="text-3xl font-bold text-slate-900">
                {{ $title }}
            </h2>
            <p class="text-slate-600">
                {{ $description }}
            </p>
        </div>

        {{-- Contenu principal de l'étape (formulaire, récap, etc.) --}}
        <div class="flex-grow">
            {{ $slot }}
        </div>

        {{-- Pied de page pour les boutons de navigation --}}
        <div class="mt-auto">
            {{ $footer }}
        </div>
    </div>

    {{-- Colonne de droite : Image (uniquement pour le layout 'split') --}}
    @if($layout === 'split')
        <div class="relative flex-center lg:col-span-4">
            <img src="{{ asset('img/onboarding-state.png') }}"
                 alt="Illustration avec un message de bienvenue"
                 class="w-[90%] rounded-tl-xl relative lg:absolute bottom-0 right-0 border-t border-l border-slate-200"
            >
        </div>
    @endif
</div>
