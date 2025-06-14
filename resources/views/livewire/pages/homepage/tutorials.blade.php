<x-guest-layout>
    <h1 role="heading" aria-level="1" class="sr-only">Tutoriels</h1>

    <!-- MENU -->
    <x-homepage.menu/>

    <!-- CONTENT -->
    <main>
        {{-- 1. Header --}}
        <section class="container bg-gray-50">
            <div class="px-4 pt-32 pb-12 lg:px-8 lg:pt-48">
                <div class="flex flex-col items-center gap-y-10 text-center">
                    <div class="flex flex-col items-center gap-y-6">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white py-1 px-3 text-sm-medium text-gray-700 hover:bg-gray-50">
                            🎥&nbsp;&nbsp;Tutoriels
                        </span>

                        <h2 role="heading" aria-level="2" class="homepage-title lg:text-6xl">
                            Vidéos et guides pour vous aider
                        </h2>

                        <p class="mx-auto max-w-2xl lg:text-lg-medium">
                            Découvrez nos tutoriels vidéo et guides détaillés pour vous accompagner dans l'utilisation de l'application. Que vous soyez un utilisateur novice du web ou bien un expert, nous avons des ressources pour vous aider à tirer le meilleur parti de notre application.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- 2. Vidéos --}}
        @php
            $tutorials = [
                [
                    'title' => 'Créer votre compte en 2 minute',
                    'description' => 'Découvrez comment vous inscrire et configurer votre espace FamilyNest. Le premier pas vers une gestion simplifiée.',
                    'videoSrc' => asset('video/tutorials/register.mp4'),
                    'imageSrc' => asset('img/homepage/tutorials/register.png'),
                    'duration' => '2:21',
                ],
                [
                    'title' => 'Ajouter votre première facture',
                    'description' => 'Manuellement ou via notre scanner intelligent (OCR), ajouter une facture n\'a jamais été aussi simple.',
                    'videoSrc' => asset('video/tutorials/create_invoice.mp4'),
                    'imageSrc' => asset('img/homepage/tutorials/create_invoice.png'),
                    'duration' => '3:25',
                ],
                [
                    'title' => 'Créer votre famille',
                    'description' => 'Invitez vos proches et définissez les rôles de chacun pour une gestion collaborative et transparente.',
                    'videoSrc' => asset('video/tutorials/create_family.mp4'),
                    'imageSrc' => asset('img/homepage/tutorials/create_family.png'),
                    'duration' => '3:03',
                ],
                [
                    'title' => 'Définir un objectif d\'épargne',
                    'description' => 'Que ce soit pour des vacances ou un projet, apprenez à créer et suivre vos objectifs financiers en famille.',
                    'videoSrc' => asset('video/tutorials/create_goal.mp4'),
                    'imageSrc' => asset('img/homepage/tutorials/create_goal.png'),
                    'duration' => '3:27',
                ],
                [
                    'title' => 'Explorer les fonctions avancées',
                    'description' => 'Tirez le meilleur de l\'application en maîtrisant les filtres et la personnalisation.',
                    'videoSrc' => asset('video/tutorials/advance_features.mp4'),
                    'imageSrc' => asset('img/homepage/tutorials/advance_features.png'),
                    'duration' => '2:40',
                ],
                [
                    'title' => 'Astuces et raccourcis clavier',
                    'description' => 'Gagnez encore plus de temps en utilisant nos raccourcis pour naviguer et gérer vos finances comme un pro.',
                    'videoSrc' => asset('video/tutorials/shortcuts.mp4'),
                    'imageSrc' => asset('img/homepage/tutorials/shortcuts.png'),
                    'duration' => '1:07',
                ],
                [
                    'title' => 'Bientôt disponible',
                    'description' => 'Nous préparons de nouveaux tutoriels pour vous aider. Revenez bientôt !',
                    'imageSrc' => null,
                    'videoSrc' => null,
                    'duration' => null,
                    'is_coming_soon' => true,
                ],
            ];

            $featuredTutorial = $tutorials[0];
            $otherTutorials = array_slice($tutorials, 1);
        @endphp

        {{-- Section Tutoriels --}}
        <section class="container">
            <div class="sm:p-6 sm:py-12 lg:px-8 lg:pb-16">
                <div class="flex flex-col gap-y-16">
                    {{-- Grille des tutoriels --}}
                    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 lg:grid-cols-3">

                        {{-- Tutoriel principal --}}
                        <a href="{{ $featuredTutorial['videoSrc'] }}"
                           target="_blank"
                           title="Regarder le tutoriel : {{ $featuredTutorial['title'] }}"
                           class="group relative lg:col-span-3 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center rounded-2xl border border-gray-200 bg-gray-100/50 p-4 lg:p-6"
                        >
                            {{-- Colonne img --}}
                            <div class="relative aspect-video w-full overflow-hidden rounded-xl">
                                <img
                                    src="{{ $featuredTutorial['imageSrc'] }}"
                                    alt="Aperçu du tutoriel : {{ $featuredTutorial['title'] }}"
                                    class="h-full w-full object-cover"
                                >
                                <div class="absolute inset-0 flex-center bg-black/10 transition-colors group-hover:bg-black/20">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/80 backdrop-blur-sm transition-transform group-hover:scale-110">
                                        <x-svg.play class="h-6 w-6 text-gray-600" />
                                    </div>
                                </div>
                            </div>
                            {{-- Colonne texte --}}
                            <div class="flex flex-col items-start gap-y-4">
                                <p class="uppercase text-xs-semibold text-gray-700">Le plus regardé !</p>
                                <h3 role="heading" aria-level="3" class="text-2xl font-semibold text-gray-900">{{ $featuredTutorial['title'] }}</h3>
                                <p class="text-gray-600">{{ $featuredTutorial['description'] }}</p>
                                <p class="text-sm text-gray-600">Durée : {{ $featuredTutorial['duration'] }}</p>
                            </div>
                        </a>

                        {{-- Autres tutoriels --}}
                        @foreach($otherTutorials as $tutorial)
                            @if(isset($tutorial['is_coming_soon']) && $tutorial['is_coming_soon'])
                                {{-- Carte "Bientôt disponible" --}}
                                <div class="flex-center flex-col gap-4 h-full min-h-[20rem] rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/80 p-8 text-center">
                                    <x-svg.clock class="h-10 w-10 text-gray-400" />
                                    <h3 role="heading" aria-level="3" class="font-semibold text-gray-900">{{ $tutorial['title'] }}</h3>
                                    <p class="text-sm text-gray-500">{{ $tutorial['description'] }}</p>
                                </div>
                            @else
                                {{-- Carte de tutoriel standard --}}
                                <a href="{{ $tutorial['videoSrc'] }}"
                                   target="_blank"
                                   title="Regarder le tutoriel : {{ $tutorial['title'] }}"
                                   class="group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-gray-100/50">
                                    <div class="relative aspect-video w-full">
                                        <img
                                            src="{{ $tutorial['imageSrc'] }}"
                                            alt="Aperçu du tutoriel : {{ $tutorial['title'] }}"
                                            class="h-full w-full object-cover"
                                        >
                                        <div class="absolute inset-0 flex-center bg-black/10 transition-colors group-hover:bg-black/20">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/80 backdrop-blur-sm transition-transform group-hover:scale-110">
                                                <x-svg.play class="h-5 w-5 text-gray-500" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-1 flex-col justify-between p-6">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $tutorial['title'] }}</h3>
                                            <p class="mt-2 text-sm text-gray-600">{{ $tutorial['description'] }}</p>
                                        </div>
                                        <p class="mt-4 text-sm text-gray-600">Durée : {{ $tutorial['duration'] }}</p>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- 3. CTA --}}
        <x-homepage.cta
            title="Les tutoriels vous ont aidé et convaincus ?"
            description="Essayez de reproduire les actions présentées dans les vidéos et guides. Si vous êtes prêt à simplifier la gestion de vos factures, commencer dès maintenant."
            buttonText="Je suis prêt à commencer"
        />

        <x-divider />
    </main>

    <x-homepage.footer />
</x-guest-layout>
