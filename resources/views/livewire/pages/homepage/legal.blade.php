<x-guest-layout>
    <x-homepage.menu />

    <main>
        <section class="container bg-gray-50">
            <div class="mx-auto max-w-3xl px-4 pb-12 lg:px-8 pt-32">
                <div class="space-y-12">
                    {{-- En-tête de la page --}}
                    <div class="space-y-4">
                        <h1 class="text-3xl font-semibold text-gray-900 sm:text-5xl">Mentions légales</h1>
                        <p class="homepage-text">Informations relatives à l'éditeur et à l'hébergement du service FamilyNest.</p>
                    </div>

                    {{-- Sections de contenu --}}
                    <div class="space-y-10 text-gray-800 leading-relaxed">
                        <div class="space-y-3">
                            <h2 class="text-xl font-semibold text-gray-900">Éditeur du site</h2>
                            <p>
                                Ce site, FamilyNest, est édité par&nbsp;: <strong>Renaud Van Meerbergen</strong><br>
                                Statut&nbsp;: Particulier (Projet réalisé dans le cadre d'un travail de fin d'études)<br>
                                Contact&nbsp;: <a href="mailto:familynest.officiel@gmail.com" class="font-medium text-indigo-600 hover:text-indigo-800">familynest.officiel@gmail.com</a>
                            </p>
                        </div>

                        <div class="space-y-3">
                            <h2 class="text-xl font-semibold text-gray-900">Hébergement</h2>
                            <p>
                                L'application FamilyNest est hébergée par&nbsp;: <strong>Laravel Cloud</strong><br>
                                Société&nbsp;: Laravel LLC<br>
                                Adresse&nbsp;: 2810 N Church St, Ste 65345, Wilmington, DE 19802-4447, USA<br>
                                Site web&nbsp;: <a href="https://family-nest.laravel.cloud" target="_blank" rel="noopener noreferrer" class="font-medium text-indigo-600 hover:text-indigo-800">https://family-nest.laravel.cloud</a>
                            </p>
                        </div>

                        <div class="space-y-3">
                            <h2 class="text-xl font-semibold text-gray-900">Propriété intellectuelle</h2>
                            <p>
                                L'ensemble de ce site, incluant sa structure, son design, son code source, ses textes et ses images, constitue une œuvre originale et est la propriété intellectuelle exclusive de Renaud Van Meerbergen. Toute reproduction, même partielle, est interdite sans autorisation écrite préalable.
                            </p>
                        </div>

                        <div class="space-y-3">
                            <h2 class="text-xl font-semibold text-gray-900">Responsabilité</h2>
                            <p>
                                FamilyNest est disponible en version 1. Bien que le plus grand soin soit apporté à sa conception, l'éditeur ne peut garantir l'absence totale d'erreurs ou d'omissions. En conséquence, l'éditeur ne saurait être tenu pour responsable de tout dommage, direct ou indirect, résultant de l'utilisation du service.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <x-homepage.footer />
</x-guest-layout>
