<x-guest-layout>
    <h1 role="heading" aria-level="1" class="sr-only">Nouveautés</h1>

    <!-- MENU -->
    <x-homepage.menu />

    <!-- CONTENT -->
    <main>
        <section class="container bg-gray-50">
            <div class="px-4 pt-32 pb-12 lg:px-8 lg:pt-48">
                <div class="flex flex-col items-center gap-y-10 text-center">
                    <div class="flex flex-col items-center gap-y-6">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white py-1 px-3 text-sm-medium text-gray-700 hover:bg-gray-50">
                            👨🏻‍💻&nbsp;&nbsp;Nouveautés
                        </span>

                        <h2 class="homepage-title lg:text-6xl">
                            Nos dernières modifications
                        </h2>

                        <p class="mx-auto max-w-2xl lg:text-lg-medium">
                            Vous trouverez ici les dernières modifications apportées à FamilyNest, ainsi que les fonctionnalités à venir. Votre retour est essentiel pour nous aider à améliorer l'application.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        @php
            $badges = [
                'new' => ['text' => 'Nouveau', 'classes' => 'bg-green-100 text-green-800'],
                'improvement' => ['text' => 'Amélioration', 'classes' => 'bg-blue-100 text-blue-800'],
                'fix' => ['text' => 'Correction', 'classes' => 'bg-red-100 text-red-800'],
            ];

            $changelogs = [
                [
                    // CHANGELOG 1 : LE SCANNER OCR
                    'date' => Carbon\Carbon::create(2025, 6, 12),
                    'changes' => [
                        [
                            'title' => 'Scannez vos factures, gagnez du temps !',
                            'description' => 'Prenez une photo ou importez un PDF, et laissez FamilyNest remplir les champs importants pour vous (fournisseur, montant, dates...). Un gain de temps considérable pour vous concentrer sur l\'essentiel.',
                            'imageSrc' => asset('img/homepage/changelog/scanner-ocr.png'),
                            'tags' => ['new'],
                        ],
                         [
                            'title' => 'Correction du calcul des montants',
                            'description' => 'Un bug pouvait causer des erreurs d\'arrondi lors de la répartition des montants entre plusieurs membres de la famille. C\'est maintenant corrigé pour des totaux toujours exacts.',
                            'imageSrc' => asset('img/homepage/changelog/amount-fix.png'),
                            'tags' => ['fix'],
                        ],
                    ],
                ],
                [
                    // CHANGELOG 2 : LA GESTION FAMILIALE
                    'date' => Carbon\Carbon::create(2025, 5, 28),
                    'changes' => [
                        [
                            'title' => 'Gérez votre budget en famille, plus simplement',
                            'description' => 'Vous pouvez désormais inviter votre conjoint(e) ou d\'autres membres dans votre espace FamilyNest, en leur attribuant des rôles (Lecteur, Éditeur) pour une gestion collaborative et sécurisée.',
                            'imageSrc' => asset('img/homepage/changelog/invite-member.png'),
                            'tags' => ['new'],
                        ],
                        [
                            'title' => 'Organisez vos factures à votre façon avec les tags',
                            'description' => 'En plus des catégories, vous pouvez maintenant ajouter des tags personnalisés à vos factures (ex: "Luminus", "Vacances2025"). Cela vous permet de retrouver et de filtrer vos dépenses selon vos propres critères, pour une organisation plus personnelle.',
                            'imageSrc' => asset('img/homepage/changelog/tag.png'),
                            'tags' => ['improvement'],
                        ],
                    ],
                ],
                [
                    // CHANGELOG 3 : LE LANCEMENT
                    'date' => Carbon\Carbon::create(2025, 5, 15),
                    'changes' => [
                        [
                            'title' => 'Bienvenue sur FamilyNest ! Lancement de la version 1',
                            'description' => 'La première version de FamilyNest est disponible. Notre mission : offrir un outil simple, beau et efficace pour que la gestion des factures ne soit plus une corvée. Nous sommes impatients de lire vos retours pour construire la suite avec vous.',
                            'imageSrc' => asset('img/homepage/changelog/launch.png'),
                            'tags' => ['new'],
                        ],
                    ],
                ],
            ];
        @endphp

        {{-- Section Changelog --}}
        <section class="container">
            <div class="px-6 py-20 sm:pb-24 sm:pt-12 lg:px-8">
                <div class="flex flex-col gap-y-16">

                    {{-- Contenu du changelog --}}
                    <div class="relative mx-auto max-w-5xl">
                        @foreach($changelogs as $log)
                            <div class="relative grid grid-cols-1 gap-x-12 pt-8 pb-16 last:pb-0 lg:grid-cols-3 border-t border-slate-200">

                                {{-- Colonne de date (sticky) --}}
                                <div class="lg:col-span-1 lg:pl-4 mb-2 lg:mb-0">
                                    <div class="sticky top-24">
                                        <p class="text-sm-medium leading-6 text-gray-500">
                                            {{ $log['date']->translatedFormat('j F Y') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Colonne des changelogs --}}
                                <div class="lg:col-span-2 space-y-12">
                                    @foreach($log['changes'] as $change)
                                        <div class="space-y-4">

                                            {{-- Titre et badges --}}
                                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                                                <h3 class="text-xl font-semibold text-gray-900">{{ $change['title'] }}</h3>
                                                <div class="flex gap-2">
                                                    @foreach($change['tags'] as $tagKey)
                                                        <span @class([
                                                    'rounded-full px-2.5 py-0.5 text-xs font-medium',
                                                    $badges[$tagKey]['classes']
                                                ])>
                                                    {{ $badges[$tagKey]['text'] }}
                                                </span>
                                                    @endforeach
                                                </div>
                                            </div>

                                            {{-- Image de la fonctionnalité --}}
                                            <div class="overflow-hidden rounded-xl border border-gray-200">
                                                <img src="{{ $change['imageSrc'] }}" alt="Illustration : {{ $change['title'] }}" class="w-full bg-white object-cover">
                                            </div>

                                            {{-- Description --}}
                                            <p class="leading-relaxed text-gray-600">{{ $change['description'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>

    <x-homepage.footer />
</x-guest-layout>
