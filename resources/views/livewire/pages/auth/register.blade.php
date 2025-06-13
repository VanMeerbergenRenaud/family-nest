<x-auth-template title="Inscription" description="Entrez vos informations pour créer un compte." showSocialLogin>

    <form wire:submit="register">
        @csrf

        <div class="flex flex-col gap-4">
            <!-- Name -->
            <x-form.field
                label="Nom d'utilisateur"
                name="name"
                model="form.name"
                placeholder="Nom"
                autocomplete="name"
                autofocus
                required
                class="capitalize"
            />

            <!-- Email -->
            <x-form.field
                label="Adresse mail"
                name="email"
                type="email"
                model="form.email"
                placeholder="votre-email@gmail.com"
                autocomplete="email"
                required
                class="lowercase"
            />

            <!-- Password -->
            <x-form.field-password
                label="Mot de passe"
                name="password"
                model="form.password"
                autocomplete="new-password"
                required
            />
        </div>

        {{-- General conditions --}}
        <div class="mt-6 px-2 grid grid-cols-[1fr_auto] items-center justify-between gap-3">
            <x-form.checkbox-input
                label="Accepter les conditions"
                name="general_conditions"
                wire:model.blur="form.general_conditions"
                checked
            />

            <button type="button" wire:click="showConditions" class="min-w-fit text-sm-medium text-gray-700 underline" title="Voir les conditions d'utilisation">
                {{ __("Conditions") }}
                <span class="max-sm:hidden text-sm-medium text-gray-700 underline">{{ __('d\'utilisation') }}</span>
            </button>
        </div>

        {{-- Error message --}}
        @error('form.general_conditions')
            <p class="my-2 flex flex-col gap-2">
                <span class="pl-2 pr-1 text-sm-medium text-red-500 dark:text-red-400">
                    {{ $message }}
                </span>
            </p>
        @enderror

        <div class="mt-8 mb-5">
            <button type="submit" class="w-full py-3.5 px-10 rounded-[10px] text-sm-medium md:text-[15px] bg-[#292A2B] text-white hover:bg-black">
                {{ __('S\'inscrire') }}
            </button>
        </div>

        <!-- Login link -->
        @if (Route::has('login'))
            <div class="text-sm-regular text-center text-dark-gray">
                {{ __('Déjà un compte ?') }}
                <a href="{{ route('login') }}"
                   class="text-sm-medium text-black link-underline"
                   title="Vers la page de connexion"
                   wire:navigate
                >
                    {{ __('Se connecter') }}
                </a>
            </div>
        @endif
    </form>

    {{-- General conditions modal --}}
    @if($showGeneralCondition)
        <x-modal wire:model="showGeneralCondition">
            <x-modal.panel>
                <div class="p-4 md:p-6">
                    <div class="grid grid-cols-[auto_1fr] gap-4">

                        <div class="max-sm:hidden ml-2 p-3 w-fit h-fit rounded-full bg-gray-100">
                            <x-svg.conditions />
                        </div>

                        <h2 role="heading" aria-level="2" role="heading" aria-level="3" class="pr-8 pl-2 text-lg-semibold md:text-xl-semibold self-center">
                            {{ __('Conditions générales d\'utilisation') }}
                        </h2>

                        <!-- Contenu sur 2 colonnes -->
                        <div class="col-span-2 pt-4 text-md-regular text-gray-500 overflow-y-auto">
                            <div class="pl-2 pr-4 lg:pr-6 max-h-96">
                                <h3 role="heading" aria-level="3" class="font-semibold mb-2">1. Introduction</h3>
                                <p class="mb-3">
                                    Bienvenue sur notre plateforme. Les présentes conditions générales d'utilisation régissent votre utilisation de notre service et constituent un accord légal entre vous et notre entreprise.
                                </p>

                                <h3 role="heading" aria-level="3" class="font-semibold mb-2">2. Acceptation des conditions</h3>
                                <p class="mb-3">
                                    En vous inscrivant et en utilisant notre service, vous acceptez d'être lié par ces conditions. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser notre service.
                                </p>

                                <!-- Reste du contenu identique -->
                                <h3 role="heading" aria-level="3" class="font-semibold mb-2">3. Description du service</h3>
                                <p class="mb-3">
                                    Notre service offre une solution de gestion de facturation en ligne permettant aux utilisateurs de créer, gérer et suivre leurs factures et clients.
                                </p>

                                <h3 role="heading" aria-level="3" class="font-semibold mb-2">4. Inscription et compte</h3>
                                <p class="mb-3">
                                    Pour utiliser notre service, vous devez créer un compte en fournissant des informations exactes et complètes. Vous êtes responsable de maintenir la confidentialité de votre mot de passe et de toutes les activités qui se produisent sous votre compte.
                                </p>

                                <h3 role="heading" aria-level="3" class="font-semibold mb-2">5. Protection des données personnelles</h3>
                                <p class="mb-3">
                                    Nous traitons vos données personnelles conformément à notre politique de confidentialité. En utilisant notre service, vous consentez à ce traitement de vos données personnelles.
                                </p>

                                <h3 role="heading" aria-level="3" class="font-semibold mb-2">6. Limites de responsabilité</h3>
                                <p class="mb-3">
                                    Notre service est fourni "tel quel" sans garantie d'aucune sorte. Nous ne serons pas responsables des dommages directs, indirects, accessoires ou consécutifs résultant de votre utilisation du service.
                                </p>

                                <h3 role="heading" aria-level="3" class="font-semibold mb-2">7. Droits de propriété intellectuelle</h3>
                                <p class="mb-3">
                                    Tous les droits de propriété intellectuelle sur le service et son contenu restent notre propriété ou celle de nos concédants de licence. Vous ne pouvez pas utiliser notre contenu sans notre autorisation écrite.
                                </p>

                                <h3 role="heading" aria-level="3" class="font-semibold mb-2">8. Résiliation</h3>
                                <p class="mb-3">
                                    Nous nous réservons le droit de résilier ou de suspendre votre compte et l'accès au service, à notre seule discrétion, sans préavis, pour toute violation des présentes conditions.
                                </p>

                                <h3 role="heading" aria-level="3" class="font-semibold mb-2">9. Modifications des conditions</h3>
                                <p class="mb-3">
                                    Nous pouvons modifier ces conditions à tout moment. Les modifications prennent effet dès leur publication. Votre utilisation continue du service après de telles modifications constitue votre acceptation des conditions modifiées.
                                </p>

                                <h3 role="heading" aria-level="3" class="font-semibold mb-2">10. Loi applicable et juridiction</h3>
                                <p class="mb-3">
                                    Ces conditions sont régies par la loi applicable dans votre pays. Tout litige relatif à ces conditions sera soumis à la juridiction exclusive des tribunaux compétents de votre pays.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <x-modal.footer class="bg-white dark:bg-gray-800 border-t border-gray-400 dark:border-gray-700">
                    <div class="flex justify-end w-full gap-3">
                        <x-modal.close>
                            <button type="button" class="button-secondary">
                                {{ __('Fermer') }}
                            </button>
                        </x-modal.close>
                    </div>
                </x-modal.footer>
            </x-modal.panel>
        </x-modal>
    @endif
</x-auth-template>
