<div>
    {{-- Formulaire pour créer une facture : multi step  --}}
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
    }" class="p-4 m-8 max-w-[900px]">

        <div class="flex justify-center mb-8 flex-wrap">
            <template x-for="(step, index) in steps" :key="index">
                <div class="flex items-center cursor-pointer" @click="goToStep(index + 1)">
                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-2"
                         :class="{ 'bg-blue-500 text-white': currentStep === index + 1, 'bg-gray-200 text-gray-500': currentStep !== index + 1 }">
                        <span x-text="index + 1"></span>
                    </div>
                    <span x-text="step" class="text-gray-600"
                          :class="{ 'font-bold': currentStep === index + 1 }"></span>
                    <span x-show="index < steps.length - 1" class="mx-4 text-gray-400">
                        <x-svg.chevron-right class="w-[1.5rem]" />
                    </span>
                </div>
            </template>
        </div>

        <form wire:submit.prevent="submit">
            @csrf

            {{-- Étape 1: Informations --}}
            <div x-show="currentStep === 1">
                <h2>Étape 1: Informations générales</h2>
                <p>Choisissez le type de facture que vous venez d'importer.</p>

                <x-form.field label="Nom de la facture" name="nom_facture" model="nom_facture" required/>
            </div>

            {{-- Étape 2: Montant --}}
            <div x-show="currentStep === 2">
                <h2>Étape 2: Montant</h2>
                <x-form.field label="Montant HT" name="montant_ht" model="montant_ht" required/>
            </div>

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
            <div class="mt-6 flex justify-between">
                <button type="button" x-show="currentStep > 1" @click="prevStep"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Précédent
                </button>
                <button type="button" x-show="currentStep < steps.length" @click="nextStep"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Suivant
                </button>
                <button type="submit" x-show="currentStep === steps.length"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Envoyer
                </button>
            </div>
        </form>
    </div>
    {{-- Fin du formulaire pour créer une facture --}}

    <div class="p-8 grid items-center justify-items-center">
        <form wire:submit.prevent="createInvoice" class="grid gap-8">
            @csrf

            {{-- Étape 1 : Importer la facture --}}
            <div class="grid grid-cols-2 gap-4 w-full">
                <div>
                    <label for="uploadedFile">Importer une facture</label>
                    <input type="file" wire:model="uploadedFile" id="uploadedFile">
                    @error('uploadedFile')
                    <span>{{ $message }}</span>
                    @enderror

                    {{-- Todo: Vérifier que ce n'est pas un pdf, fonctionne que pour les img --}}
                    @if ($uploadedFile)
                        <img src="{{ $uploadedFile->temporaryUrl() }}"
                             alt="Image temporaire de la preview de la facture"/>
                    @endif
                </div>
            </div>

            {{-- Étape 2 : Remplir les informations --}}
            <div class="form__content">
                <x-form.field label="Nom de la facture" name="name" model="name"/>
                <x-form.field label="Fournisseur / Émetteur" name="issuer" model="issuer"/>
                <x-form.field label="Type de facture" name="type" model="type"/>
                <x-form.field label="Catégorie de la facture" name="category" model="category"/>
                <x-form.field label="Site internet du fournisseur" name="website" type="url" model="website"/>

                <div>
                    <label for="amount">Montant (€)</label>
                    <input type="text" x-mask:dynamic="$money($input, '.', ' ')" wire:model="amount"
                           id="amount">
                    @error('amount') <span>{{ $message }}</span> @enderror
                </div>

                <div>
                    <label>Montant variable</label>
                    <div>
                        <input type="radio" wire:model="is_variable" id="is_variable_yes" value="1">
                        <label for="is_variable_yes">Oui</label>
                    </div>
                    <div>
                        <input type="radio" wire:model="is_variable" id="is_variable_no" value="0">
                        <label for="is_variable_no">Non</label>
                    </div>
                </div>

                <div>
                    <label>Associé à un membre de la famille</label>
                    <div>
                        <input type="radio" wire:model="is_family_related" id="is_family_related_yes" value="1">
                        <label for="is_family_related_yes">Oui</label>
                    </div>
                    <div>
                        <input type="radio" wire:model="is_family_related" id="is_family_related_no" value="0">
                        <label for="is_family_related_no">Non</label>
                    </div>
                </div>

                <x-form.field
                    label="Date d'émission"
                    name="issued_date"
                    type="date"
                    model="issued_date"
                    min="2020-01-01T00:00"
                    placeholder="{{ now()->format('d-m-Y') }}"
                />

                <x-form.field label="Rappels de paiement" name="payment_reminder" model="payment_reminder"/>
                <x-form.field label="Fréquence de paiement" name="payment_frequency" model="payment_frequency"/>

                <div>
                    <label for="status">Statut de la facture</label>
                    <select wire:model="status" id="status">
                        <option value="unpaid">Non-payée</option>
                        <option value="paid">Payée</option>
                        <option value="late">En retard</option>
                        <option value="partially_paid">Partiellement payée</option>
                    </select>
                    @error('status') <span>{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="payment_method">Méthode de paiement</label>
                    <select wire:model.blur="payment_method" id="payment_method">
                        <option value="cash">Cash</option>
                        <option value="card">Bancontact</option>
                        <option value="mastercard">Visa/Mastercard</option>
                    </select>
                    @error('payment_method') <span>{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="priority">Priorité</label>
                    <select wire:model="priority" id="priority">
                        <option value="high">Élevée</option>
                        <option value="medium">Moyenne</option>
                        <option value="low">Basse</option>
                    </select>
                    @error('priority') <span>{{ $message }}</span> @enderror
                </div>

                <x-form.field-textarea label="Notes" name="notes" model="notes"
                                       placeholder="Ajoutez des notes ici"/>

                <div class="mb-4 flex flex-col gap-2">
                    <label for="tagInput" class="block text-sm font-medium text-gray-700">Tags</label>
                    <div class="mt-1 flex rounded-md">
                        <input type="text" wire:model="tagInput" id="tagInput"
                               class="border py-2 px-4 border-b-gray-200 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Ajouter un tag">
                        <button type="button" wire:click="addTag"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-r-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Ajouter
                        </button>
                    </div>

                    @error('tags') <span class="text-sm text-red-600">{{ $message }}</span> @enderror

                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($tags as $index => $tag)
                            <span
                                class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                    {{ $tag }}
                                    <button type="button" wire:click="removeTag({{ $index }})"
                                            class="ml-2 inline-flex items-center justify-center h-4 w-4 rounded-full bg-indigo-200 text-indigo-600 hover:bg-indigo-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <span class="sr-only">Supprimer tag</span>
                                        <x-svg.cross class="h-3 w-3"/>
                                    </button>
                                </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <x-modal.footer>
                <x-modal.close>
                    <button type="button" class="cancel">
                        {{ __('Annuler') }}
                    </button>
                </x-modal.close>

                <button type="submit" class="save">
                    {{ __('Créer') }}
                </button>
            </x-modal.footer>
        </form>
    </div>
</div>
