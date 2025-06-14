<x-guest-layout>
    <x-homepage.menu/>

    <main>
        <section class="container bg-gray-50">
            <div class="mx-auto max-w-3xl px-4 pb-16 sm:pb-24 lg:px-8 pt-32">
                <div class="space-y-10">
                    {{-- En-tête de la page --}}
                    <div class="space-y-4">
                        <h1 class="text-3xl font-semibold text-gray-900 sm:text-5xl">
                            Politique de confidentialité
                        </h1>
                        <p class="homepage-text text-gray-500">
                            <span class="text-sm-medium">v1.0</span>
                            <span> - 1er octobre 2023</span>
                        </p>
                        <p class="homepage-text">
                            Chez FamilyNest, votre vie privée est notre priorité. Cette politique explique de manière
                            simple et transparente quelles données nous collectons et pourquoi, comment nous les
                            utilisons et les protégeons.
                        </p>
                    </div>

                    {{-- Table des matières --}}
                    <div class="space-y-4">
                        <h2 class="text-2xl font-semibold text-gray-900">Sommaire</h2>
                        <ul class="list-disc list-inside space-y-2 text-indigo-600">
                            <li>
                                <a href="#responsable" class="font-medium hover:text-indigo-800">
                                    1. Responsable du traitement
                                </a>
                            </li>
                            <li>
                                <a href="#donnees" class="font-medium hover:text-indigo-800">
                                    2. Les données que nous collectons
                                </a>
                            </li>
                            <li>
                                <a href="#finalites" class="font-medium hover:text-indigo-800">
                                    3. Pourquoi nous collectons vos données
                                </a>
                            </li>
                            <li>
                                <a href="#partage" class="font-medium hover:text-indigo-800">
                                    4. Partage et services tiers
                                </a>
                            </li>
                            <li>
                                <a href="#conservation" class="font-medium hover:text-indigo-800">
                                    5. Durée de conservation
                                </a>
                            </li>
                            <li>
                                <a href="#droits" class="font-medium hover:text-indigo-800">
                                    6. Vos droits sur vos données
                                </a>
                            </li>
                            <li>
                                <a href="#cookies" class="font-medium hover:text-indigo-800">
                                    7. Notre politique sur les cookies
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- Sections de contenu avec des ID pour les ancres --}}
                    <div class="space-y-10 text-gray-800 leading-relaxed">
                        <div id="responsable" class="scroll-mt-24 space-y-3">
                            <h2 class="text-xl font-semibold text-gray-900">
                                1. Responsable du traitement des données</h2>
                            <p>
                                Le responsable du traitement de vos données est <strong>Renaud Van Meerbergen</strong>.
                                Pour toute question, vous pouvez le contacter à
                                <a href="mailto:familynest.officiel@gmail.com"
                                   class="font-medium text-indigo-600 hover:text-indigo-800">
                                    familynest.officiel@gmail.com
                                </a>.
                            </p>
                        </div>

                        <div id="donnees" class="scroll-mt-24 space-y-3">
                            <h2 class="text-xl font-semibold text-gray-900">
                                2. Les données que nous collectons
                            </h2>
                            <p>
                                Nous collectons uniquement les données dont nous avons besoin pour que FamilyNest fonctionne correctement :
                            </p>
                            <ul class="list-disc list-inside space-y-2">
                                <li>
                                    <strong>Données d'identification :</strong> Votre nom et votre adresse mail lors de votre inscription.
                                </li>
                                <li>
                                    <strong>Données d'utilisation :</strong> Les informations que vous nous confiez&nbsp;: détails de factures, objectifs, documents importés, etc.
                                </li>
                                <li>
                                    <strong>Données techniques :</strong> Votre adresse IP, uniquement pour des raisons de sécurité et de maintenance.
                                </li>
                            </ul>
                        </div>

                        <div id="finalites" class="scroll-mt-24 space-y-3">
                            <h2 class="text-xl font-semibold text-gray-900">
                                3. Pourquoi nous collectons vos données
                            </h2>
                            <p>
                                <strong>Notre engagement est simple : vos données servent uniquement à vous rendre service.</strong>
                                Nous les utilisons pour :
                            </p>
                            <ul class="list-disc list-inside space-y-2">
                                <li>Faire fonctionner l'application (vous connecter, stocker vos factures).</li>
                                <li>Vous envoyer des notifications utiles (rappels de paiement, etc.).</li>
                                <li>Protéger votre compte.</li>
                            </ul>
                            <p class="font-semibold">
                                Nous ne vendrons, ne louerons et ne partagerons jamais vos données personnelles à des fins commerciales.
                            </p>
                        </div>

                        <div id="partage" class="scroll-mt-24 space-y-3">
                            <h2 class="text-xl font-semibold text-gray-900">
                                4. Partage des données et services tiers
                            </h2>
                            <p>Nous faisons appel à des services tiers reconnus pour leur fiabilité et leur sécurité afin de faire fonctionner FamilyNest&nbsp;:
                            </p>
                            <ul class="list-disc list-inside space-y-2">
                                <li>
                                    <strong>Amazon Web Services (AWS) :</strong>
                                    Pour stocker vos fichiers de manière sécurisée et chiffrée.
                                </li>
                                <li>
                                    <strong>Infomaniak :</strong>
                                    Pour envoyer les mails d'inscription et de notifications.
                                </li>
                            </ul>
                        </div>

                        <div id="conservation" class="scroll-mt-24 space-y-3">
                            <h2 class="text-xl font-semibold text-gray-900">
                                5. Durée de conservation
                            </h2>
                            <p>
                                Nous conservons vos données tant que votre compte est actif. Si vous le supprimez, toutes vos informations
                                (compte, factures, documents) seront définitivement effacées de nos serveurs sous 30 jours.
                            </p>
                        </div>

                        <div id="droits" class="scroll-mt-24 space-y-3">
                            <h2 class="text-xl font-semibold text-gray-900">
                                6. Vos droits sur vos données
                            </h2>
                            <p>`
                                Vous avez le contrôle total sur vos données. Conformément au RGPD, vous pouvez à tout moment&nbsp;:
                            </p>
                            <ul class="list-disc list-inside space-y-2">
                                <li><strong>Accéder</strong> à vos données depuis votre compte.</li>
                                <li><strong>Rectifier</strong> vos informations directement dans l'application.</li>
                                <li><strong>Demander l'effacement</strong> complet de votre compte.</li>
                            </ul>
                            <p>Pour exercer ces droits, contactez-nous simplement par mail.</p>
                        </div>

                        <div id="cookies" class="scroll-mt-24 space-y-3">
                            <h2 class="text-xl font-semibold text-gray-900">7. Notre politique sur les cookies</h2>
                            <p>
                                <strong>Nous n'utilisons aucun cookie de suivi, de publicité ou d'analyse.</strong>
                                Le seul cookie utilisé est un cookie de session, indispensable pour vous maintenir connecté(e)
                                à votre compte pendant votre visite. Votre navigation sur FamilyNest est et restera privée.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <x-homepage.footer/>
</x-guest-layout>
