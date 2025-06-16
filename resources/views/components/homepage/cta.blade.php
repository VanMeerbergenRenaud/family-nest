@props([
    'title' => 'Rejoignez l\'aventure FamilyNest',
    'description' => 'Faites partie de cette histoire en créant votre compte gratuitement. Ensemble, construisons un outil qui simplifie réellement la gestion de vos factures familiales et vous apporte de la sérénité au quotidien.',
    'buttonText' => 'Tester cette version bêta',
    'buttonUrl',
])

<section {{ $attributes->merge(['class' => 'bg-slate-100']) }}>
    <div class="container py-10">
        <div class="relative isolate mx-auto max-w-5xl overflow-hidden rounded-3xl border border-slate-200 bg-slate-200/35 px-6 py-16 text-center sm:px-16 sm:py-20">
            <div class="mx-auto flex max-w-2xl flex-col items-center gap-y-6">
                <h2 class="text-2xl sm:text-4xl md:text-5xl font-semibold text-gray-900">
                    {{ $title }}
                </h2>
                <p class="homepage-text">
                    {{ $description }}
                </p>
                <div class="mt-2 flex-center">
                    <a href="{{ $buttonUrl ?? route('register') }}" wire:navigate class="button-secondary">
                        {{ $buttonText }}
                        <x-svg.arrows.right class="text-white" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
