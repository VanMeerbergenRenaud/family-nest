@php
    $mainLinks = [];

    $links = [
        [
            'title' => 'Navigation',
            'links' => [
                ['name' => 'À propos', 'url' => route('about')],
                ['name' => 'Tutoriels', 'url' => route('tutorials'), 'is_new' => true],
                ['name' => 'Nouveautés', 'url' => route('changelog')],
                ['name' => 'Prix', 'url' => route('pricing'), 'is_disabled' => true],
            ],
        ],
        [
            'title' => 'Application',
            'links' => [
                ['name' => 'S\'inscrire', 'url' => route('register')],
                ['name' => 'Se connecter', 'url' => route('login')],
            ],
        ],
        [
            'title' => 'Ressources',
            'links' => [
                ['name' => 'Laravel Cloud', 'url' => 'https://cloud.laravel.com', 'is_external' => true],
                ['name' => 'AWS Console', 'url' => 'https://aws.amazon.com/console', 'is_external' => true],
                ['name' => 'Infomaniak', 'url' => 'https://www.infomaniak.com', 'is_external' => true],
                ['name' => 'ILovePDF', 'url' => 'https://www.ilovepdf.com', 'is_external' => true],
                ['name' => 'Livewire', 'url' => 'https://livewire.laravel.com', 'is_external' => true],
                ['name' => 'Tailwind', 'url' => 'https://tailwindcss.com', 'is_external' => true],
            ],
        ],
        [
            'title' => 'Support',
            'links' => [
                ['name' => 'Contacter l\'équipe', 'url' => 'mailto:familynest.officiel@gmail.com'],
                ['name' => 'Signaler un bug', 'url' => 'mailto:familynest.officiel@gmail.com'],
            ],
        ],
    ];

    $mainLinks = collect($links)->map(function ($column) {
        $column['links'] = collect($column['links'])->map(function ($link) {
            $link['is_new'] = $link['is_new'] ?? false;
            $link['is_disabled'] = $link['is_disabled'] ?? false;
            $link['is_external'] = $link['is_external'] ?? false;
            return $link;
        })->all();
        return $column;
    })->all();

    $legalLinks = [
        ['name' => 'Mentions légales', 'url' => route('legal')],
        ['name' => 'Politique de confidentialité', 'url' => route('privacy')],
    ];
@endphp

<footer class="bg-slate-100 text-gray-700" aria-labelledby="footer-heading">
    <h2 role="heading" aria-level="2" class="sr-only">Menu de bas de page</h2>

    {{-- PARTIE SUPÉRIERIEURE : Logo et liens principaux --}}
    <div class="container pt-12 pb-16">
        <div class="mx-auto px-4 flex max-w-5xl flex-col gap-10 lg:grid lg:grid-cols-5 lg:gap-8">
            {{-- Logo --}}
            <div class="lg:ml-2 lg:col-span-1">
                <a href="/" aria-label="Accueil" wire:navigate class="flex items-center gap-3 text-lg-semibold">
                    <x-app.logo class="h-7 w-auto"/>
                    FamilyNest
                </a>
            </div>

            {{-- Colonnes de liens --}}
            <div class="lg:ml-4 lg:mt-1 grid grid-cols-2 gap-8 sm:grid-cols-3 lg:col-span-4 lg:grid-cols-4">
                {{-- On accède maintenant à la propriété calculée du composant --}}
                @foreach ($mainLinks as $column)
                    <div class="flex flex-col gap-y-6">
                        <h3 role="heading" aria-level="3" class="text-md-semibold text-gray-900">{{ $column['title'] }}</h3>
                        <ul role="list" class="flex flex-col gap-y-4">
                            @foreach ($column['links'] as $link)
                                <li>
                                    <a href="{{ $link['url'] }}"
                                       wire:navigate
                                       @if($link['is_external']) target="_blank" rel="noopener noreferrer" @endif
                                       title="Voir la page {{ $link['name'] }}"
                                       class="group inline-flex flex-wrap items-center gap-2 text-gray-600 transition-all duration-300 hover:text-gray-900 @if($link['is_disabled']) cursor-not-allowed opacity-60 @endif"
                                    >
                                        <span>{{ $link['name'] }}</span>
                                        @if($link['is_new'])
                                            <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs-medium text-indigo-700">Nouveau</span>
                                        @endif
                                        @if($link['is_disabled'])
                                            <span class="mt-0.25 rounded-full bg-gray-200 px-2 py-0.5 text-xs-medium text-gray-700">Prochainement</span>
                                        @endif
                                        @if($link['is_external'])
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="h-3 w-3 text-gray-400 group-hover:text-gray-700">
                                                <path d="M4.75 2.75a.75.75 0 0 0 0 1.5h6.538L2.144 13.394a.75.75 0 0 0 1.06 1.06L12.25 5.412V12a.75.75 0 0 0 1.5 0V2.75a.75.75 0 0 0-.75-.75H4.75Z"/>
                                            </svg>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <x-divider/>

    {{-- PARTIE INFÉRIEURE : Copyright et liens légaux --}}
    <div class="p-6 sm:px-12">
        <div class="flex flex-col items-center gap-3 sm:gap-4 text-sm text-gray-600 lg:flex-row lg:justify-between">
            <p class="text-sm">© {{ date('Y') }} FamilyNest. Tous droits réservés.</p>
            <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6">
                {{-- On accède maintenant à la propriété calculée du composant --}}
                @foreach($legalLinks as $link)
                    <a href="{{$link['url']}}"
                       wire:navigate
                       class="text-sm hover:text-gray-900"
                       title="Voir la page {{ $link['name'] }}"
                    >
                        {{ $link['name'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
