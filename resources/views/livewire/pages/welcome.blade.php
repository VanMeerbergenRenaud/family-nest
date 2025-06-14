<x-guest-layout>
    <h1 role="heading" aria-level="1" class="sr-only">FamilyNest : l'application qui simplifie la gestion de vos factures familiales</h1>

    <!-- MENU -->
    <x-homepage.menu />

    <!-- PHP CONTENT -->
    @php
        $tabs = [
            ['id' => 'dashboard', 'label' => 'Tableau de bord', 'icon' => 'svg.home', 'imageSrc' => asset('img/homepage/app/dashboard.png')],
            ['id' => 'invoices', 'label' => 'Factures', 'icon' => 'svg.invoice', 'imageSrc' => asset('img/homepage/app/invoices.png')],
            ['id' => 'archives', 'label' => 'Archives', 'icon' => 'svg.archive', 'imageSrc' => asset('img/homepage/app/archives.png')],
            ['id' => 'goals', 'label' => 'Objectifs', 'icon' => 'svg.tag', 'imageSrc' => asset('img/homepage/app/goals.png')],
            ['id' => 'family', 'label' => 'Famille', 'icon' => 'svg.user', 'imageSrc' => asset('img/homepage/app/family.png')],
        ];

        $defaultActiveTab = $tabs[0]['id'] ?? '';

        $sponsors = [
            ['name' => 'Laravel Cloud', 'logo' => 'laravel-cloud', 'url' => 'https://cloud.laravel.com'],
            ['name' => 'Laravel', 'logo' => 'laravel', 'url' => 'https://laravel.com'],
            ['name' => 'Livewire', 'logo' => 'livewire', 'url' => 'https://livewire.laravel.com'],
            ['name' => 'Alpine.js', 'logo' => 'alpinejs', 'url' => 'https://alpinejs.dev'],
            ['name' => 'Tailwind CSS', 'logo' => 'tailwindcss', 'url' => 'https://tailwindcss.com'],
            ['name' => 'Infomaniak', 'logo' => 'infomaniak', 'url' => 'https://www.infomaniak.com'],
            ['name' => 'AWS', 'logo' => 'aws', 'url' => 'https://aws.amazon.com'],
            ['name' => 'PhpStorm', 'logo' => 'phpstorm', 'url' => 'https://www.jetbrains.com/phpstorm/'],
            ['name' => 'ILovePDF', 'logo' => 'ilovepdf', 'url' => 'https://www.ilovepdf.com'],
        ];

        $features = [
            ['title' => '1. Créer votre compte', 'description' => 'En seulement quelques clics, enregistrez-vous et accédez à l\'application. Vérifiez vos mails et puis le tour est joué. Il ne vous reste plus qu\'à créer votre nouvelle famille.', 'image' => asset('img/homepage/features/step_1.png')],
            ['title' => '2. Ajouter votre première facture', 'description' => 'Importez vos factures depuis vos mails ou votre ordinateur. FamilyNest les organise pour vous, prêtes à être consultées à tout moment.', 'image' => asset('img/homepage/features/step_2.png')],
            ['title' => '3. Fixez-vous un objectif', 'description' => 'Grâce à la fonctionnalité d\'objectifs, vous pouvez définir des budgets mensuels ou annuels pour mieux gérer vos finances familiales.', 'image' => asset('img/homepage/features/step_3.png')],
        ];

        $bentoInfos = [
            ['title' => 'Tableau de bord et statistiques', 'description' => 'Visualisez vos dépenses, suivez vos factures et obtenez des statistiques détaillées sur vos finances familiales. Filtrez par date, membre de la famille ou par statut de paiement en un clin d\'œil et observez les résultats.', 'imageSrc' => asset('img/homepage/tools/stats.png'), 'columnSpan' => 2],
            ['title' => 'Reconnaissance automatique', 'description' => 'Notre technologie OCR vous permet d\'extraire automatiquement les informations importantes de vos factures, pour une gestion encore plus rapide.', 'imageSrc' => asset('img/homepage/tools/ocr.png'), 'columnSpan' => 1],
            ['title' => 'Accès rapide', 'description' => 'Ajoutez une nouvelle facture ou consultez un document important en un clin d\'œil, où que vous soyez.', 'imageSrc' => asset('img/homepage/tools/barre_recherche.png'), 'columnSpan' => 1],
            ['title' => 'Centralisation intelligente', 'description' => 'Importez facilement vos factures depuis vos mails ou votre ordinateur. Tout est centralisé, classé et prêt à être consulté.', 'imageSrc' => asset('img/homepage/tools/integrations.png'), 'columnSpan' => 1],
            ['title' => 'Vue d\'ensemble', 'description' => 'Suivez vos prochaines échéances et le total de vos dépenses d\'un seul coup d\'œil depuis votre tableau de bord.', 'imageSrc' => asset('img/homepage/tools/widgets.png'), 'columnSpan' => 1],
        ];

        $testimonials = [
            ['quote' => 'T\'es le meilleur ! Tu designs super bien et tu passes beaucoup de temps sur ton appli. Prends un peu plus de temps pour moi ! Sinon, j\'adore l\'utiliser. Tu pourrais juste améliorer certaines couleurs, je t\'expliquerai. ❤️', 'name' => 'Amélie R.', 'title' => 'Ma copine', 'image' => asset('img/homepage/testimonials/amélie.jpeg')],
            ['quote' => 'Olala, je fais vraiment n\'importe quoi, j\'ai des erreurs partout ! Il faut que tu m\'aides et que tu me montres comment faire, car je suis perdue. Par contre, j\'adorerais pouvoir l\'utiliser car je ne risque pas de me tromper avec toutes ces restrictions. 😅', 'name' => 'Monique D.', 'title' => 'Ma mamy', 'image' => asset('img/homepage/testimonials/monique.png')],
            ['quote' => 'Cela a dépassé toutes mes attentes. C\'est super simple à utiliser et tellement efficace. Ça m\'a vraiment simplifié la vie, merci petit couz.', 'name' => 'Alexandre K.', 'title' => 'Mon cousin préféré', 'image' => asset('img/homepage/testimonials/alexandre.png')],
            ['quote' => 'Je ne suis vraiment pas doué avec un ordinateur et je ne sais même pas utiliser ton Mac... mais j\'ai tout de même réussi à créer une facture seul, donc c\'est que ton application est bien ! Mais tu continueras de faire mes paiements par QR Code, car je n\'y arrive toujours pas 🤣.', 'name' => 'Eddy V.', 'title' => 'Le daron', 'image' => asset('img/homepage/testimonials/eddy.jpg')],
            ['quote' => 'Je suis certainement l\'utilisatrice la plus active sur l\'application à ce jour, j\'ai presque tout testé et j\'adore vraiment toutes les fonctionnalités présentées. Merci à toi d\'avoir développé cette application, elle m\'est d\'une aide précieuse.', 'name' => 'France M.', 'title' => 'Ma maman', 'image' => asset('img/homepage/testimonials/france.jpg')],
            ['quote' => 'Salut Renaud, je viens de tester ça. Bravo à toi, c\'est très propre ! Très agréable à utiliser. J\'ai juste eu deux petits problèmes "techniques" au moment de créer la facture. L\'aperçu de ma facture PDF n\'a pas fonctionné quand je l\'ai importée et je n\'arrivais pas à répartir mon montant 🫤.', 'name' => 'Florian R.', 'title' => 'Indépendant & menuisier', 'image' => asset('img/homepage/testimonials/florian.png')],
            ['quote' => 'À prendre ou à laisser mais perso je mettrai les boutons en disabled avec le style disabled si tu n’as pas de fonctionnalités derrière. Sinon les gens au PFE vont s’énerver et croire que ça ne marche pas. Sinon c’est cleeeeeaaaaaaan j\'adore !', 'name' => 'Julien M.', 'title' => 'Développeur incroyable', 'image' => asset('img/homepage/testimonials/justin.png')],
            ['quote' => 'Salut, j\'ai enfin testé ton application et je la trouve vraiment sympa. Si seulement je pouvais avoir des applis aussi intuitives pour faire mes expertises, ce serait trop bien. Beau boulot !', 'name' => 'Anthonin V.', 'title' => 'Expert comptable', 'image' => asset('img/homepage/testimonials/anthonin.png')],
            ['quote' => 'C\'est exactement ce que je cherchais. C\'est direct, efficace et le design est très agréable. Je l\'achèterai dès que ça sortira, c\'est certain !', 'name' => 'Anissa F.', 'title' => 'Voisine architecte', 'image' => asset('img/homepage/testimonials/anissa.png')],
        ];

        $faqs = [
            ['question' => 'Qui se cache derrière ce projet ?', 'answer' => 'FamilyNest est un projet développé avec passion par le développeur Renaud Van Meerbergen, pour son travail de fin d\'études, avec l\'ambition d\'en faire un outil réellement utile pour les familles. Chaque retour et chaque suggestion comptent énormément pour l\'améliorer !'],
            ['question' => 'Mes données personnelles sont-elles en sécurité ?', 'answer' => 'Oui, la sécurité et la confidentialité de vos données sont notre priorité absolue. Nous utilisons des protocoles de chiffrement et les meilleures pratiques du secteur pour garantir que vos informations restent protégées et ne sont jamais partagées.'],
            ['question' => 'Quel type d\'aide proposez-vous en cas de problème ?', 'answer' => 'Nous sommes là pour vous aider. Si vous avez une question ou une suggestion, vous pouvez nous contacter par mail. Étant une petite structure, nous nous engageons à vous répondre personnellement dans les meilleurs délais.'],
            ['question' => 'Comment fonctionne la tarification de FamilyNest ?', 'answer' => 'FamilyNest est actuellement en version bêta, et son utilisation est entièrement gratuite. Nous prévoyons d\'introduire des offres payantes à l\'avenir pour couvrir les frais de serveur, mais il y aura toujours une offre gratuite généreuse.'],
            ['question' => 'Pourrai-je annuler un abonnement facilement ?', 'answer' => 'Absolument. Le jour où nous proposerons des abonnements, vous pourrez les gérer et les annuler à tout moment depuis votre tableau de bord, sans frais cachés ni engagement à long terme. La transparence est essentielle pour nous.'],
        ];

        $faqCount = count($faqs);
    @endphp

    {{-- VIEW CONTENT --}}
    <main>
        {{-- 1. Hero Section --}}
        <section class="container bg-gray-50">
            <div class="px-4 pt-32 pb-12 lg:px-8 lg:pt-48">
                <div x-data="{
                        tabs: {{ json_encode($tabs) }},
                        activeTab: '{{ $defaultActiveTab }}',
                        get activeTabData() {
                            if (!this.tabs) return { label: '', icon: '' };
                            return this.tabs.find(tab => tab.id === this.activeTab);
                        }
                    }"
                     class="flex flex-col items-center gap-y-10 text-center"
                >
                    {{-- Hero Content (inchangé) --}}
                    <div class="flex flex-col items-center gap-y-6">
                        <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white py-1 pl-4.5 pr-3 text-sm-medium text-gray-700 hover:bg-gray-50" title="Vers la page d'inscription">
                            🚀&nbsp;&nbsp;Disponible en version bêta
                            <x-svg.arrows.right class="h-3 w-3"/>
                        </a>
                        <h1 role="heading" aria-level="2" class="homepage-title lg:text-6xl">
                            Organisation <br>familiale simplifiée.
                        </h1>
                        <p class="mx-auto max-w-2xl lg:text-lg-medium">
                            FamilyNest est l'application qui vous aide à gérer vos factures familiales, à suivre vos dépenses et à garder le contrôle de votre budget.
                        </p>
                        <div class="flex-center flex-wrap gap-4">
                            <a href="{{ route('register') }}" class="button-primary" wire:navigate title="Vers la page d'inscription">
                                Créer un compte gratuit
                            </a>
                            <a href="mailto:familynest.officiel@gmail.com" class="button-secondary" title="Vers votre gestionnaire de mail">
                                Nous contacter
                            </a>
                        </div>
                    </div>

                    {{-- Tabs Component --}}
                    <div class="flex w-full flex-col items-center gap-4 sm:gap-6 sm:mt-16">

                        {{-- Desktop : Navigation par boutons --}}
                        <ul role="tablist" aria-label="Fonctionnalités du produit" class="hidden flex-wrap justify-center gap-2 lg:flex">
                            @foreach($tabs as $tab)
                                <li wire:key="{{ $tab['id'] }}-tab-desktop">
                                    <button
                                        id="{{ $tab['id'] }}-tab-desktop"
                                        role="tab"
                                        type="button"
                                        aria-controls="{{ $tab['id'] }}-panel"
                                        :aria-selected="activeTab === '{{ $tab['id'] }}'"
                                        @click="activeTab = '{{ $tab['id'] }}'"
                                        :class="{
                                            'bg-white text-gray-900 ring-1 ring-inset ring-gray-300/80': activeTab === '{{ $tab['id'] }}',
                                            'bg-gray-50 text-gray-600 hover:bg-gray-100 hover:text-gray-900': activeTab !== '{{ $tab['id'] }}'
                                        }"
                                        class="flex items-center gap-x-2 rounded-lg border border-transparent px-3 py-[7px] text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500"
                                    >
                                        <x-dynamic-component :component="$tab['icon']" class="h-5 w-5" />
                                        <span>{{ $tab['label'] }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        {{-- Mobile : Dropdown menu --}}
                        <div class="flex-center lg:hidden">
                            <x-menu>
                                <x-menu.button class="button-primary gap-3 px-4">
                                    <span class="relative h-4 w-4">
                                        @foreach ($tabs as $tab)
                                            <span x-show="activeTab === '{{ $tab['id'] }}'" x-cloak>
                                                <x-dynamic-component :component="$tab['icon']" class="h-4 w-4" aria-hidden="true" />
                                            </span>
                                        @endforeach
                                    </span>
                                    <span class="flex-grow" x-text="activeTabData.label"></span>
                                    <x-svg.chevron-down class="ml-1" aria-hidden="true" />
                                </x-menu.button>

                                <x-menu.items class="w-54">
                                    @foreach($tabs as $tab)
                                        <x-menu.close>
                                            <x-menu.item
                                                wire:key="{{ $tab['id'] }}-tab-mobile"
                                                @click="activeTab = '{{ $tab['id'] }}'"
                                            >
                                                <x-dynamic-component :component="$tab['icon']" class="w-4.5 h-4.5" aria-hidden="true"/>
                                                <span>{{ $tab['label'] }}</span>
                                            </x-menu.item>
                                        </x-menu.close>
                                    @endforeach
                                </x-menu.items>
                            </x-menu>
                        </div>

                        {{-- Conteneur des images --}}
                        <div class="relative w-full rounded-xl border border-gray-200">
                            @foreach($tabs as $tab)
                                <div x-show="activeTab === '{{ $tab['id'] }}'" x-transition:enter="transition ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" id="{{ $tab['id'] }}-panel" role="tabpanel" :aria-labelledby="`${activeTab}-tab-desktop`" class="w-full" x-cloak>
                                    <img src="{{ $tab['imageSrc'] }}" alt="Aperçu de la fonctionnalité {{ $tab['label'] }}" class="w-full rounded-xl">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 2. Technologies --}}
        <section class="container">
            <div class="mx-auto max-w-5xl p-8">
                <div class="flex flex-col items-center gap-y-12">
                    <h2 role="heading" aria-level="2" class="text-center text-2xl font-semibold leading-tight text-gray-900">
                        Réaliser avec des technologies fiables
                    </h2>
                    <div class="group relative w-full overflow-hidden [mask-image:linear-gradient(to_right,transparent,white_10%,white_90%,transparent)]">
                        <div class="flex gap-8 animate-marquee group-hover:[animation-play-state:paused]">
                            @foreach (range(1, 2) as $iteration)
                                <div class="flex min-w-full flex-shrink-0 items-center justify-around gap-x-10" @if($iteration > 1) aria-hidden="true" @endif>
                                    @foreach ($sponsors as $sponsor)
                                        <a href="{{ $sponsor['url'] }}" tabindex="-1" target="_blank" rel="noopener noreferrer" title="{{ $sponsor['name'] }}" class="text-gray-400 transition hover:text-gray-800">
                                            <x-dynamic-component :component="'svg.logos.' . $sponsor['logo']" class="min-h-8 w-auto"/>
                                            <span class="sr-only">{{ $sponsor['name'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 3. Features Grid --}}
        <section class="container">
            <div class="px-6 py-24 sm:py-32 lg:px-8">
                <div class="flex flex-col gap-y-10 sm:gap-y-16 lg:gap-y-24">
                    <x-homepage.section-header
                        badge-text="Fonctionnalités magiques"
                        badge-icon="🧙🏻"
                        badge-classes="bg-neutral-200/75 text-neutral-700"
                        title="Gérez vos factures et dépenses familiales en toute simplicité"
                        description="Oubliez les piles de papiers, les mails égarés et les applications compliquées. FamilyNest centralise tout pour vous en quelques étapes seulement."
                    />

                    <dl class="mx-auto grid max-w-xl grid-cols-1 gap-6 lg:gap-y-16 lg:max-w-none lg:grid-cols-3">
                        @foreach ($features as $feature)
                            <div class="flex flex-col gap-y-6">
                                <div class="flex-center h-80 overflow-hidden p-3 bg-white rounded-2xl border border-slate-200">
                                    <img
                                        src="{{ $feature['image'] }}"
                                        alt="Aperçu: {{ $feature['title'] }}"
                                        class="h-full text-center object-contain bg-white rounded-xl border border-slate-200"
                                    >
                                </div>
                                <div class="px-2 flex flex-auto flex-col gap-y-1">
                                    <dt class="text-base font-semibold leading-7 text-gray-900">{{ $feature['title'] }}</dt>
                                    <dd class="flex flex-auto flex-col gap-y-6 text-base leading-7 text-gray-600">
                                        <p class="flex-auto">{{ $feature['description'] }}</p>
                                    </dd>
                                </div>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>
        </section>

        {{-- 4. Alternating Blocks --}}
        <section class="container bg-gray-50 py-20 sm:py-24">
            <div class="flex flex-col gap-y-20 px-2 sm:gap-y-24 lg:px-0">

                {{-- PREMIER BLOC : Tableau des factures --}}
                <x-homepage.block-img
                    image-position="right"
                    badge-icon="📑"
                    badge-text="Tableau des factures"
                    badge-classes="bg-sky-200/75 text-slate-600/90"
                    title="Toutes vos factures, parfaitement organisées"
                    description="Organiser, triez et visualisez vos factures à votre bon vouloir. Payées, en attente, par date ou par montant : retrouvez l'information dont vous avez besoin en un seul coup d'œil, sans vous perdre dans les détails."
                    :image-src="asset('img/homepage/prototype/table.png')"
                    url="{{ route('changelog') }}"
                />

                {{-- DEUXIÈME BLOC : Recherche --}}
                <x-homepage.block-img
                    image-position="left"
                    badge-icon="🔎"
                    badge-text="Recherche puissante"
                    badge-classes="bg-rose-100 text-rose-700/80"
                    title="Retrouvez n'importe quoi, instantanément"
                    description="Notre barre de recherche vous permet de trouver une facture, un document ou un membre de la famille en quelques touches. C'est rapide, intelligent et super efficace."
                    :image-src="asset('img/homepage/prototype/search.png')"
                    url="{{ route('changelog') }}"
                />
            </div>
        </section>

        {{-- 5. Bento Grid --}}
        <section class="container">
            <div class="sm:px-4 py-20 sm:py-24 lg:px-8">
                <div class="flex flex-col gap-y-10 sm:gap-y-16 lg:gap-y-24">
                    <x-homepage.section-header
                        badge-text="Des outils pour vous"
                        badge-icon="⚒️"
                        badge-classes="bg-gray-200/50 text-gray-700"
                        title="Des outils conçus pour vous simplifier la vie"
                        description="Prenez le contrôle de vos finances familiales avec des fonctionnalités qui vous apportent clarté, simplicité et efficacité au quotidien."
                    />
                    <dl class="mx-auto grid max-w-2xl grid-cols-1 gap-8 lg:max-w-none lg:grid-cols-3">
                        @foreach ($bentoInfos as $feature)
                            <div @class(['flex flex-col justify-between gap-y-6 rounded-3xl bg-white p-6', 'lg:col-span-2' => $feature['columnSpan'] == 2])>
                                <div class="relative flex-center max-h-72 overflow-hidden">
                                    <img src="{{ $feature['imageSrc'] }}" alt="Illustration: {{ $feature['title'] }}" class="h-full w-full object-cover text-center bg-transparent">
                                </div>
                                <div class="flex flex-col gap-y-2">
                                    <dt class="text-base font-semibold text-gray-900">{{ $feature['title'] }}</dt>
                                    <dd class="text-sm text-gray-600">{{ $feature['description'] }}</dd>
                                </div>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>
        </section>

        {{-- 6. Mockup Section --}}
        <section class="container overflow-hidden">
            <div class="px-6 py-24 sm:py-32 lg:px-8">
                <div class="mx-auto grid max-w-2xl grid-cols-1 items-center gap-x-12 gap-y-16 sm:gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-2">
                    <div class="lg:order-last">
                        <div class="flex max-w-lg flex-col items-start gap-y-6">
                            <p class="inline-flex items-center py-1 pl-4 pr-3.5 gap-2.5 text-sm-medium rounded-full border border-gray-200 bg-slate-200/50 text-gray-600">
                                <span>💻</span>
                                Accessible partout
                            </p>
                            <h2 role="heading" aria-level="2" class="homepage-title">
                                Conçue pour fonctionner sur tous vos appareils
                            </h2>
                            <p class="text-lg leading-8 text-gray-600">
                                Que vous soyez sur ordinateur, tablette ou smartphone, accédez à vos informations de manière sécurisée, à tout moment.
                            </p>
                            <div class="flex flex-wrap items-center gap-3">
                                <a href="{{ route('register') }}" wire:navigate class="button-secondary">Commencer</a>
                                <a href="{{ route('changelog') }}" class="homepage-link h-auto">
                                    En savoir plus
                                    <x-svg.arrow-left class="ml-1 rotate-180"/>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-start justify-center lg:justify-start">
                        <img src="{{ asset('img/homepage/app/mockup.png') }}" alt="Aperçu de l'application FamilyNest sur mobile" class="pointer-events-none scale-125 bg-transparent">
                    </div>
                </div>
            </div>
        </section>

        {{-- 7. Testimonials --}}
        <section id="testimonials-section" class="container">
            <div class="sm:px-4 py-20 sm:py-24 lg:px-8">
                <div class="flex flex-col gap-y-12">
                    <x-homepage.section-header
                        badge-text="Témoignages"
                        badge-icon="💬"
                        badge-classes="bg-neutral-200/75 text-neutral-600"
                        title="Des familles déjà conquises"
                        description="Découvrez pourquoi les premiers utilisateurs ont adopté FamilyNest pour gérer leurs finances au quotidien."
                    />
                    <div x-data="{ shown: false }" class="relative">
                        <div class="columns-1 gap-8 lg:columns-3" :class="!shown ? 'max-h-[65rem] overflow-hidden' : 'max-h-none'" style="transition: max-height 0.7s ease-in-out;">
                            @foreach ($testimonials as $testimonial)
                                <div class="mb-8 break-inside-avoid">
                                    <figure class="flex h-full flex-col gap-y-6 rounded-2xl border border-slate-200 bg-white p-6">
                                        <blockquote class="flex-grow text-gray-700">
                                            <p>"{{ $testimonial['quote'] }}"</p>
                                        </blockquote>
                                        <figcaption class="flex items-center gap-3">
                                            <img src="{{ $testimonial['image'] }}" alt="Avatar de {{ $testimonial['name'] }}" class="h-8 w-8 rounded-full bg-gray-50 object-cover">
                                            <div>
                                                <div class="text-sm-medium">{{ $testimonial['name'] }}</div>
                                                <div class="text-sm-regular text-gray-600">{{ $testimonial['title'] }}</div>
                                            </div>
                                        </figcaption>
                                    </figure>
                                </div>
                            @endforeach
                        </div>
                        <div x-show="!shown" x-cloak x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-72 bg-gradient-to-t from-gray-50 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-12 flex justify-center">
                                <button type="button" @click="shown = true" class="button-primary">Voir plus d'avis</button>
                            </div>
                        </div>
                        <div x-show="shown" x-cloak class="mt-6 flex justify-center">
                            <button type="button" @click="shown = false" class="button-primary">Voir moins</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 8. FAQ --}}
        <section class="container" aria-labelledby="faq-title">
            <div class="mx-auto max-w-4xl px-6 py-12 sm:py-16 lg:px-8 mb-12">
                <div class="flex flex-col gap-y-16">
                    <x-homepage.section-header
                        badge-text="FAQ"
                        badge-classes="bg-purple-100 text-purple-700"
                        title="Vos questions, nos réponses"
                        description="Vous avez des interrogations ? Nous avons les réponses. Si vous ne trouvez pas votre bonheur, n'hésitez pas à nous écrire."
                    >
                        <a href="mailto:familynest.officiel@gmail.com" class="button-primary w-fit" title="Vers votre gestionnaire de mail">
                            Poser une question
                            <x-svg.arrows.right />
                        </a>
                    </x-homepage.section-header>

                    <div x-data="{ openFaqs: [0], get allOpen() { return this.openFaqs.length === {{ $faqCount }}; } }">
                        <div class="relative mb-6">
                            <div class="absolute bottom-6 right-2">
                                <x-form.checkbox-input name="expand-all" label="Tout afficher" @change="openFaqs = $event.target.checked ? Array.from({length: {{ $faqCount }}}, (_, i) => i + 1) : []"/>
                            </div>
                        </div>
                        <dl class="space-y-4">
                            @foreach ($faqs as $faq)
                                @php $id = $loop->iteration; @endphp
                                <div class="border-b border-gray-200 pb-4" x-data="{ id: {{ $id }}, get isOpen() { return openFaqs.includes(this.id); }, toggle() { if (this.isOpen) { openFaqs = openFaqs.filter(i => i !== this.id); } else { openFaqs.push(this.id); } } }">
                                    <dt>
                                        <button @click="toggle()" :aria-expanded="isOpen" aria-controls="faq-answer-{{ $id }}" id="faq-question-{{ $id }}" type="button" class="group flex w-full items-start justify-between text-left text-gray-700">
                                            <span class="text-md-semibold transition-colors group-hover:text-slate-900">{{ $faq['question'] }}</span>
                                            <span class="ml-6 flex h-7 items-center">
                                                <svg x-show="isOpen" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5"><path fill-rule="evenodd" d="M4 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 10Z" clip-rule="evenodd"/></svg>
                                                <svg x-show="!isOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/></svg>
                                            </span>
                                        </button>
                                    </dt>
                                    <dd id="faq-answer-{{ $id }}" :aria-labelledby="'faq-question-{{ $id }}'" class="pr-12" x-show="isOpen" x-collapse x-cloak>
                                        <p class="mt-2 text-gray-600">{{ $faq['answer'] }}</p>
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>
            </div>
        </section>

        {{-- 9. CTA --}}
        <x-homepage.cta
            title="Prêt(e) à simplifier vos finances familiales ?"
            description="Créez votre compte gratuitement et découvrez comment FamilyNest peut vous aider à reprendre le contrôle de votre budget et à gagner en sérénité."
            buttonText="Essayer FamilyNest gratuitement"
        />

        <x-divider/>
    </main>
    <!-- END VIEW CONTENT -->

    <!-- FOOTER -->
    <x-homepage.footer />
</x-guest-layout>
