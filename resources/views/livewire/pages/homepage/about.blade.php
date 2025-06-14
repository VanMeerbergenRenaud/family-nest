<x-guest-layout>
    <h1 role="heading" aria-level="1" class="sr-only">À propos</h1>

    <!-- MENU -->
    <x-homepage.menu />

    <!-- CONTENT -->
    <main>
        <section class="container bg-gray-50">
            <div class="px-4 pt-32 pb-12 lg:px-8 lg:pt-48">
                <div class="flex flex-col items-center gap-y-10 text-center">
                    <div class="flex flex-col items-center gap-y-6">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white py-1 px-3 text-sm-medium text-gray-700 hover:bg-gray-50">
                            📖&nbsp;&nbsp;Mon histoire
                        </span>

                        <h2 class="homepage-title lg:text-6xl">
                            À propos du mystérieux créateur
                        </h2>

                        <p class="mx-auto max-w-2xl lg:text-lg-medium">
                            La gestion des factures familiales est devenue un vrai casse-tête. FamilyNest a été créé pour y mettre fin et j'ai l'ambition de vous offrir un outil simple, intuitif et efficace.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Mot créateur --}}
        <section class="container">
            <div class="mx-auto max-w-7xl px-6 pt-4 pb-16 sm:py-16 md:py-24 lg:px-8">
                <div class="sm:grid grid-cols-1 flex flex-col-reverse items-start gap-x-16 gap-y-12 lg:grid-cols-2">

                    {{-- Colonne de gauche --}}
                    <div class="flex flex-col gap-y-8">

                        <div class="space-y-6 text-lg leading-8 text-gray-600">
                            <h2 class="homepage-title text-balance">Petit mot du créateur</h2>
                            <p>
                                FamilyNest n'est pas né dans une salle de réunion, mais d'une simple conversation avec ma maman. Elle me confiait à quel point il était devenu compliqué de s'y retrouver entre les factures papier, les mails et les différentes applications. J'ai alors compris qu'il y avait un réel besoin de simplifier la gestion des factures familiales, un besoin que beaucoup d'entre nous ressentent au quotidien.
                            </p>
                            <p>
                                Ce projet, commencé dans le cadre de mes études, est rapidement devenu une véritable aventure. Mon objectif était de créer une application moderne, intuitive et simple d'utilisation, pensée pour des familles comme la mienne et non pour des experts-comptables. Un outil qui apporte de la sérénité dans la gestion quotidienne des factures familiales.
                            </p>
                            <p>
                                Chaque suggestion et chaque retour que je reçois durant cette phase bêta est incroyablement précieux. Merci de faire partie de cette aventure et de m'aider à construire un outil qui, je l'espère, vous sera réellement utile au quotidien.
                            </p>
                        </div>

                        {{-- Bloc signature --}}
                        <div class="flex justify-between items-center gap-6">
                            <div>
                                <p class="text-sm-medium">Renaud Van Meerbergen</p>
                                <p class="mt-1 text-sm text-gray-600">Fondateur de FamilyNest</p>
                            </div>
                            <img src="{{ asset('img/homepage/about/signature.png') }}" alt="Signature de Renaud Van Meerbergen" class="bg-transparent h-14 w-auto">
                        </div>
                    </div>

                    <img class="mt-4 w-full rounded-2xl object-cover min-h-[55vh]" src="{{ asset('img/homepage/about/me.jpeg') }}" alt="Photo de Renaud Van Meerbergen, le fondateur de FamilyNest">
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <x-homepage.cta
            title="Rejoignez l'aventure FamilyNest"
            description="Faites partie de cette histoire en créant votre compte gratuitement. Ensemble, construisons un outil qui simplifie réellement la gestion de vos factures familiales et vous apporte de la sérénité au quotidien."
            buttonText="Rejoindre la communauté"
        />

        <x-divider/>
    </main>

    <x-homepage.footer />
</x-guest-layout>
