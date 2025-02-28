<div>
    {{-- Etape impérative 1 : Formualire d'importation de la facture --}}


    {{-- Etape impérative 2 : Formulaire pour créer une facture : multi step  --}}
    <div x-data="{
        currentStep: 1,
        steps: ['Informations', 'Montant', 'Dates', 'Engagements', 'Paiement', 'Notes', 'Résumé'],
        nextStep() {
            this.currentStep++;
        },
        prevStep() {
            this.currentStep--;
        },
        goToStep(step) {
            this.currentStep = step;
        }
    }" class="mx-auto max-w-[70rem]">

        {{-- Barre de progression avec les étapes --}}
        <div class="py-4 px-6 rounded-xl max-lg:bg-gray-100 dark:max-lg:bg-gray-800 flex flex-col items-start lg:flex-row lg:items-center max-lg:gap-3 mb-6 space-x-2 overflow-x-scroll scrollbar-hidden">
            <template x-for="(step, index) in steps" :key="index">
                <div class="flex-center cursor-pointer whitespace-nowrap" @click="goToStep(index + 1)">
                    <span class="w-8 h-8 rounded-full flex-center mr-3"
                         :class="{ 'bg-slate-700 text-white': currentStep === index + 1, 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200': currentStep !== index + 1 }">
                        <span x-text="index + 1" class="text-sm"></span>
                    </span>

                    <span class="text-md-regular text-slate-700 dark:text-slate-200"
                         :class="{ 'font-medium underline': currentStep === index + 1 }"
                         x-text="step"></span>

                    <span x-show="index < steps.length - 1" class="mx-2 text-slate-400 dark:text-slate-500">
                        <x-svg.chevron-right class="w-4 h-4 md:w-6 md:h-6"/>
                    </span>
                </div>
            </template>
        </div>

        <form wire:submit.prevent="submit">
            @csrf

            {{-- Facture preview--}}
            <div class="max-lg:flex-center gap-4 lg:grid lg:grid-cols-[30vw_auto] lg:gap-12">

                {{-- Image : colonne 1 --}}
                <div class="max-lg:hidden overflow-hidden flex-center max-h-[75vh] max-w-[30vw]">
                    @if (!$uploadedFile)
                        <x-form.field-upload label="Importer une facture" model="uploadedFile" name="uploadedFile" />
                    @else
                        <img src="{{ $uploadedFile->temporaryUrl() }}"
                             alt="Image temporaire de la preview de la facture"
                             class="rounded-2xl h-full min-h-[30rem]"
                        />
                    @endif
                </div>

                {{-- Steps : colonne 2 --}}
                <div class="mt-6 lg:max-w-[60vw]">

                    {{-- Étape 1: Informations --}}
                    <x-invoice-create-step
                        step="1" title="Étape 1 : Informations générales"
                        description="Choisissez le type de facture que vous venez d'importer."
                    >
                        <x-form.field label="Nom de la facture" name="nom_facture" model="nom_facture"/>

                        <x-form.select label="Type" name="type_facture" model="type_facture">
                            <option value="abonnement">Abonnement</option>
                            <option value="achat_unique">Achat unique</option>
                        </x-form.select>

                        <x-form.select label="Catégorie" name="categorie_facture" model="categorie_facture">
                            <option value="internet_telecom">Internet & télécommunication</option>
                            <option value="energie">Énergie</option>
                        </x-form.select>

                        <x-form.field label="Fournisseur / émetteur de la facture" name="fournisseur_facture"
                                      model="fournisseur_facture"/>

                        <x-form.field label="Site internet du fournisseur" name="site_fournisseur"
                                      model="site_fournisseur"/>
                    </x-invoice-create-step>

                    {{-- Étape 2: Montant --}}
                    <x-invoice-create-step
                        step="2" title="Étape 2 : Montant"
                        description="Indiquez le montant de la facture et les taxes associées."
                    >
                        <x-form.field label="Montant de la facture" name="montant_facture" model="montant_facture"/>

                        <x-form.field label="Taxes" name="taxes_facture" model="taxes_facture"/>
                    </x-invoice-create-step>

                    {{-- Étape 3: Dates --}}
                    <div x-show="currentStep === 3">
                        <h2>Étape 3: Dates</h2>
                    </div>

                    {{-- Étape 4: Engagements --}}
                    <div x-show="currentStep === 4">
                        <h2>Étape 4: Engagements</h2>
                    </div>

                    {{-- Étape 5: Paiement --}}
                    <div x-show="currentStep === 5">
                        <h2>Étape 5: Paiement</h2>
                    </div>

                    {{-- Étape 6: Notes --}}
                    <div x-show="currentStep === 6">
                        <h2>Étape 6: Notes</h2>
                    </div>

                    {{-- Étape 7: Résumé --}}
                    <div x-show="currentStep === 7">
                        <h2>Étape 7: Résumé</h2>
                    </div>

                    {{-- Boutons de Navigation --}}
                    <div class="mt-6 border-t-[0.1rem] border-dashed border-gray-200 dark:border-gray-700">
                        <div class="mt-6 flex justify-between">
                            <button type="button" x-show="currentStep > 1" @click="prevStep" class="mr-4 inline-flex items-center text-sm-medium rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 -ml-1 h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                                </svg>
                                Précédent
                            </button>
                            <button type="button" x-show="currentStep < steps.length" @click="nextStep" class="inline-flex items-center text-sm-medium rounded-lg border border-blue-900 bg-blue-900 px-4 py-2 text-white hover:bg-blue-800 dark:bg-blue-900 dark:border-blue-900 dark:hover:bg-blue-800">
                                Suivant
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-2 -mr-1 h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </button>
                            <button type="submit" x-show="currentStep === steps.length" class="inline-flex items-center text-sm-medium rounded-lg bg-purple-500 px-4 py-2 text-white hover:bg-purple-600 dark:bg-purple-600 dark:hover:bg-purple-700">
                                Valider
                            </button>
                        </div>
                    </div>
                    {{-- Fin colonne 2 --}}
                </div>
            </div>
        </form>
    </div>
</div>
