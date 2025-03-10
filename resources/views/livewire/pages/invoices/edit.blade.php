<div>

    @if($isEditMode)
        <div class="mx-auto max-w-[65rem] p-3 mb-8 rounded-md bg-blue-100 border-l-4 border-blue-500 text-blue-700">
            <div class="flex-center">
                <x-svg.edit class="w-5 h-5 mr-2 text-blue-700" />
                <span class="font-medium">Mode édition de la facture</span>
            </div>
        </div>
    @endif

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
        }" class="mx-auto max-w-[70rem]">

        {{-- Barre de progression avec les étapes --}}
        <div
            class="py-4 px-6 rounded-xl max-lg:bg-gray-100 dark:max-lg:bg-gray-800 flex flex-col items-start lg:flex-row lg:items-center max-lg:gap-3 mb-6 space-x-2 overflow-x-scroll scrollbar-hidden">
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

        <form wire:submit.prevent="updateInvoice">
            @csrf

            {{-- Facture preview--}}
            <div class="max-lg:flex-center gap-4 lg:grid lg:grid-cols-[30vw_auto] lg:gap-10">

                {{-- Image : colonne 1 --}}
                <div class="mt-8 max-lg:hidden overflow-hidden flex-center max-h-[75vh] max-w-[30vw] relative">
                    @if (!$form->uploadedFile && !$form->existingFilePath)
                        <x-form.field-upload label="Importer une nouvelle facture" model="form.uploadedFile" name="form.uploadedFile" />
                    @else
                        <div class="relative w-full h-full">
                            <!-- Button de suppression de l'image -->
                            <button type="button"
                                    wire:click="removeUploadedFile"
                                    class="absolute top-3 right-3 z-2"
                            >
                                <x-svg.cross class="text-red-600 hover:text-black bg-red-300 hover:bg-red-400 rounded-full w-6 h-6 p-1 transition-colors duration-200" />
                            </button>

                            <!-- Aperçu de l'image -->
                            @if($form->uploadedFile)
                                <!-- Nouvellement téléchargé -->
                                <img src="{{ $form->uploadedFile->temporaryUrl() }}"
                                     alt="Nouvelle facture"
                                     class="rounded-2xl h-full min-h-[30rem] border border-slate-200"
                                />
                            @else
                                <!-- Fichier existant -->
                                <img src="{{ $form->existingFilePath }}"
                                     alt="Facture actuelle"
                                     class="rounded-2xl h-full min-h-[30rem] border border-slate-200"
                                />
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Steps : colonne 2 --}}
                <div class="mt-6 lg:max-w-[60vw]">

                    {{-- Étape 1: Informations générales --}}
                    <x-invoice-create-step
                        step="1" title="Étape 1 : Informations générales"
                        description="Choisissez le type de facture que vous venez d'importer."
                        class="grid grid-cols-1 gap-4"
                    >
                        <x-form.field label="Nom" name="form.name" model="form.name" placeholder="ex : Facture Internet - Octobre 2024" :asterix="true" />

                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 ">
                            <x-form.select label="Type*" name="form.type" model="form.type" label="Type">
                                <option value="" selected>Sélectionner un type</option>
                                @foreach($invoiceTypes as $typeValue => $typeLabel)
                                    <option value="{{ $typeValue }}">{{ $typeLabel }}</option>
                                @endforeach
                            </x-form.select>

                            <x-form.select label="Catégorie*" name="form.category" model="form.category" label="Catégorie">
                                <option value="" selected>Sélectionner une catégorie</option>
                                @foreach($form->availableCategories as $availableCategory)
                                    <option value="{{ $availableCategory }}">{{ $availableCategory }}</option>
                                @endforeach
                            </x-form.select>

                            <x-form.field label="Fournisseur / émetteur de la facture*" name="form.issuer_name"
                                          model="form.issuer_name" placeholder="Nom du fournisseur"/>
                            <x-form.field label="Site internet du fournisseur" name="form.issuer_website"
                                          model="form.issuer_website" placeholder="www.monfournisseur.com"/>
                        </div>

                    </x-invoice-create-step>

                    {{-- Étape 2: Détails financiers --}}
                    <x-invoice-create-step
                        step="2" title="Étape 2 : Détails financiers"
                        description="Choisissez le type de facture que vous venez d'importer."
                        class="grid grid-cols-1 lg:grid-cols-2 gap-4"
                    >
                        <x-form.field-currency
                            label="Montant total à payer"
                            name="form.amount"
                            model="form.amount"
                            defaultCurrency="EUR"
                            placeholder="0,00"
                            asterix="true"
                            :initialValue="$form->amount"
                        />

                        <x-form.select name="form.paid_by" model="form.paid_by" label="Qui paye la facture">
                            <option value="" disabled>Sélectionner une personne</option>
                            @foreach($family_members as $member)
                                <option value="{{ $member->name }}">{{ $member->name }}</option>
                            @endforeach
                        </x-form.select>

                        <x-form.select name="form.associated_members" model="form.associated_members"
                                       label="Associé à un membre de la famille">
                            <option value="" disabled>Sélectionner un membre</option>
                            @foreach($family_members as $member)
                                <option value="{{ $member->name }}">{{ $member->name }}</option>
                            @endforeach
                        </x-form.select>
                    </x-invoice-create-step>

                    {{-- Étape 3: Dates importantes --}}
                    <x-invoice-create-step
                        step="3" title="Étape 3 : Dates importantes"
                        description="Indiquez les dates importantes concernant cette facture."
                    >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-form.field-date label="Date d'émission" name="form.issued_date" model="form.issued_date" />
                            <x-form.field-date label="Date de paiement" name="form.payment_due_date" model="form.payment_due_date"/>
                            <x-form.field-date label="Rappel de paiement" name="form.payment_reminder" model="form.payment_reminder"/>
                            <x-form.select label="Fréquence de paiement" name="form.payment_frequency" model="form.payment_frequency">
                                <option value="" disabled>Sélectionner une fréquence</option>
                                <option value="monthly">Mensuel</option>
                                <option value="quarterly">Trimestriel</option>
                                <option value="annually">Annuel</option>
                                <option value="one_time">Ponctuel</option>
                            </x-form.select>
                        </div>
                    </x-invoice-create-step>

                    {{-- Étape 4: Association à des engagements --}}
                    <x-invoice-create-step
                        step="4" title="Étape 4 : Association à des engagements"
                        description="Associez cette facture à un engagement existant ou créez-en un nouveau."
                        class="flex items-end gap-4"
                    >
                        <x-form.select name="form.engagement_id" model="form.engagement_id" label="Engagement existant">
                            <option value="" disabled>Sélectionner un engagement existant</option>
                            @foreach($engagements as $engagement)
                                <option value="{{ $engagement['id'] }}">{{ $engagement['name'] }}</option>
                            @endforeach
                        </x-form.select>

                        <button type="button" class="button-tertiary border border-slate-200 w-fit">
                            Créer un nouvel engagement
                        </button>
                    </x-invoice-create-step>

                    {{-- Étape 5: Statut de paiement --}}
                    <x-invoice-create-step
                        step="5" title="Étape 5 : Statut de paiement"
                        description="Indiquez le statut actuel de paiement de cette facture."
                        class="grid grid-cols-1 lg:grid-cols-2 gap-4"
                    >
                        <x-form.select name="form.payment_status" model="form.payment_status" label="Statut de la facture">
                            <option value="unpaid">Non payée</option>
                            <option value="paid">Payée</option>
                            <option value="late">En retard</option>
                            <option value="partially_paid">Partiellement payée</option>
                        </x-form.select>


                        <x-form.select name="form.payment_method" model="form.payment_method" label="Méthode de paiement utilisée">
                            <option value="card">Carte bancaire</option>
                            <option value="cash">Espèces</option>
                            <option value="transfer">Virement</option>
                        </x-form.select>

                        <x-form.select name="form.priority" model="form.priority" label="Étiquette de priorité">
                            <option value="none">Aucune</option>
                            <option value="low">Basse</option>
                            <option value="medium">Moyenne</option>
                            <option value="high">Élevée</option>
                        </x-form.select>
                    </x-invoice-create-step>

                    {{-- Étape 6: Notes et tags personnalisés --}}
                    <x-invoice-create-step
                        step="6" title="Étape 6 : Notes et tags personnalisés"
                        description="Ajoutez des notes et des tags pour mieux organiser vos factures."
                    >

                        <x-form.field-textarea label="Notes (détail / commentaire important)" name="form.notes" model="form.notes" placeholder="Inscrivez votre message ici..."/>

                        <div class="text-right text-xs text-gray-500 mt-1 mr-2">
                            Montant de caractère maximum <span class="text-sm">{{ strlen($form->notes) }}</span>/500
                        </div>

                        <div class="mt-2">
                            <label class="block text-sm-medium text-gray-700 dark:text-gray-300">
                                Tags personnalisés
                            </label>
                            <div class="flex mt-2">
                                <input type="text" wire:model="form.tagInput" wire:keydown.enter.prevent="addTag"
                                       class="flex-1 block w-full text-sm-regular rounded-l-md bg-white border border-slate-200 dark:border-gray-600 dark:text-white p-3 focus:outline-0"
                                       placeholder="Ajouter un tag...">
                                <button type="button" wire:click="addTag"
                                        class="inline-flex items-center px-4 py-2 text-sm-medium bg-white border border-l-0 border-slate-200 rounded-r-md bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                    Ajouter un tag
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-1.5 ml-2">
                                @foreach($tags as $index => $tag)
                                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                        {{ $tag }}
                                        <button type="button" wire:click="removeTag({{ $index }})"
                                                class="ml-2 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">
                                            <x-svg.cross class="h-4 w-4"/>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </x-invoice-create-step>

                    <x-invoice-create-step
                        step="7" title="Étape 7 : Résumé"
                        description="Vérifiez les informations avant d'enregistrer la facture."
                    >
                        <!-- Affichage des erreurs de validation -->
                        @if($errors->any())
                            <x-form.alert type="warning" title="Attention : corrections requises">
                                Veuillez corriger toutes les erreurs avant de soumettre le formulaire.
                                Vous pouvez naviguer vers les étapes précédentes pour effectuer les corrections nécessaires.
                            </x-form.alert>

                            <x-form.alert type="error" title="Veuillez corriger les erreurs suivantes avant de continuer :">
                                <ul class="space-y-1 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>

                                <x-slot name="actions">
                                    <button type="button"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm-medium text-white bg-red-600 hover:bg-red-700"
                                            @click="goToStep(1)">
                                        Retourner au début du formulaire
                                    </button>
                                </x-slot>
                            </x-form.alert>
                        @elseif(empty($form->uploadedFile && $form->existingFilePath) || !$form->uploadedFile)
                            <x-form.alert type="warning" title="Aucune facture importée">
                                Veuillez importer une facture pour commencer.
                            </x-form.alert>
                        @else
                            <x-form.alert type="success" title="Prêt à soumettre">
                                Toutes les informations sont complètes. Vous pouvez maintenant enregistrer cette facture.
                            </x-form.alert>
                        @endif

                        <!-- Résumé du formulaire -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                            <dl>
                                <x-invoices.summary-item label="Nom de la facture" :alternateBackground="true">
                                    {{ ucfirst($form->name) ?: 'Non spécifié' }}
                                </x-invoices.summary-item>

                                <x-invoices.summary-item label="Type et catégorie">
                                    {{ ucfirst($form->type) ?: 'Non spécifié' }} - {{ ucfirst($form->category) ?: 'Non spécifié' }}
                                </x-invoices.summary-item>

                                <x-invoices.summary-item label="Fournisseur" :alternateBackground="true">
                                    {{ $form->issuer_name ?: 'Non spécifié' }}
                                    @if($form->issuer_website)
                                        <a href="{{ $form->issuer_website }}" target="_blank"
                                           class="text-indigo-500 text-sm-regular hover:text-indigo-700 ml-0.5">
                                            ({{ $form->issuer_website }})
                                        </a>
                                    @endif
                                </x-invoices.summary-item>

                                <x-invoices.summary-item label="Montant">
                                    @if($form->amount !== null && $form->amount !== '')
                                        <span class="text-sm-medium">{{ number_format((float)$form->amount, 2, ',', ' ') }}</span>

                                        @php
                                            $currencySymbols = [
                                                'EUR' => '€',
                                                'USD' => '$',
                                                'GBP' => '£',
                                                'JPY' => '¥',
                                                'CHF' => 'CHF',
                                                'CAD' => '$'
                                            ];
                                            $symbol = $currencySymbols[$form->currency] ?? $form->currency;
                                        @endphp

                                        <span class="ml-1 text-sm-medium">{{ $symbol }} ({{ $form->currency ?? 'EUR' }})</span>
                                    @else
                                        Non spécifié
                                    @endif
                                </x-invoices.summary-item>

                                <x-invoices.summary-item label="Répartition du montant" :alternateBackground="true">
                                    @if(!empty($form->amount))
                                        @if($form->paid_by)
                                            <span class="block">
                                                <span class="text-sm-regular">Payée par :&nbsp;</span><span class="text-sm-medium">{{ $form->paid_by }}</span>
                                            </span>
                                        @endif
                                        @if($form->associated_members)
                                            <span class="block">
                                                <span class="text-sm-regular">Associée à :&nbsp;</span><span class="text-sm-medium">{{ $form->associated_members }}</span>
                                            </span>
                                        @endif
                                    @else
                                        Non spécifié
                                    @endif
                                </x-invoices.summary-item>

                                <x-invoices.summary-item label="Dates">
                                    <div class="flex flex-col gap-1.5">
                                        <p class="text-sm-regular">
                                            Émission: <span class="text-sm-medium">
                                            {{ $form->issued_date ? \Carbon\Carbon::parse($form->issued_date)->format('d/m/Y') : 'Non spécifiée' }}
                                        </span>
                                        </p>
                                        <p class="text-sm-regular">
                                            Paiement: <span class="text-sm-medium">
                                            {{ $form->payment_due_date ? \Carbon\Carbon::parse($form->payment_due_date)->format('d/m/Y') : 'Non spécifiée' }}
                                        </span>
                                        </p>
                                        <p class="text-sm-regular">
                                            Rappel: <span class="text-sm-medium">
                                                {{ $form->getFormattedReminderAttribute() }}
                                            </span>
                                        </p>
                                        <p class="text-sm-regular">
                                            Fréquence: <span class="text-sm-medium">
                                            {{ $form->payment_frequency
                                                ? ($form->payment_frequency === 'monthly' ? 'Mensuel'
                                                : ($form->payment_frequency === 'quarterly' ? 'Trimestriel'
                                                : ($form->payment_frequency === 'annually' ? 'Annuel' : 'Ponctuel')))
                                                : 'Non spécifiée' }}
                                        </span>
                                        </p>
                                    </div>
                                </x-invoices.summary-item>

                                <x-invoices.summary-item label="Engagement" :alternateBackground="true">
                                    {{ $form->engagement_name ?: 'Non spécifié' }}
                                </x-invoices.summary-item>

                                <x-invoices.summary-item label="Statut de paiement">
                                    <span class="mb-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                          {{ $form->payment_status === 'paid'
                                                ? 'bg-green-100 text-green-800'
                                                : ($form->payment_status === 'late' ? 'bg-red-100 text-red-800'
                                                : ($form->payment_status === 'partially_paid' ? 'bg-yellow-100 text-yellow-800'
                                                : 'bg-gray-100 text-gray-800')) }}"
                                    >
                                        {{ $form->payment_status === 'paid' ? 'Payée'
                                        : ($form->payment_status === 'late' ? 'En retard'
                                        : ($form->payment_status === 'partially_paid' ? 'Partiellement payée'
                                        : 'Non payée')) }}
                                    </span>
                                    <div class="mt-1 ml-1 flex flex-col gap-1.5">
                                        <p class="text-sm-regular">
                                            Méthode: <span class="text-sm-medium">
                                                {{ $form->payment_method
                                                    ? ($form->payment_method === 'card' ? 'Carte bancaire'
                                                    : ($form->payment_method === 'cash' ? 'Espèces' : 'Virement'))
                                                    : 'Non spécifiée' }}
                                            </span>
                                        </p>
                                        <p class="text-sm-regular">
                                            Priorité: <span class="text-sm-medium">
                                                {{ $form->priority
                                                    ? ($form->priority === 'high' ? 'Élevée'
                                                    : ($form->priority === 'medium' ? 'Moyenne'
                                                    : ($form->priority === 'low' ? 'Basse' : 'Aucune')))
                                                    : 'Non spécifiée' }}
                                            </span>
                                        </p>
                                    </div>
                                </x-invoices.summary-item>

                                <x-invoices.summary-item label="Notes" :alternateBackground="true">
                                    <span class="text-sm-regular">{{ $form->notes ?: 'Aucune note' }}</span>
                                </x-invoices.summary-item>

                                <x-invoices.summary-item label="Tags">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($tags as $tag)
                                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                {{ $tag }}
                                            </span>
                                        @empty
                                            Aucun tag
                                        @endforelse
                                    </div>
                                </x-invoices.summary-item>

                                <x-invoices.summary-item label="Pièce jointe" :alternateBackground="true">
                                    @if($form->uploadedFile)
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="h-5 w-5 mr-2 text-green-500" viewBox="0 0 20 20"
                                                 fill="currentColor">
                                                <path fill-rule="evenodd"
                                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Facture importée avec succès
                                        </div>
                                    @else
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="h-5 w-5 mr-2 text-red-500" viewBox="0 0 20 20"
                                                 fill="currentColor">
                                                <path fill-rule="evenodd"
                                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            Aucune facture importée
                                        </div>
                                    @endif
                                </x-invoices.summary-item>
                            </dl>
                        </div>
                    </x-invoice-create-step>

                    {{-- Boutons de Navigation --}}
                    <div class="my-6 border-t-[0.1rem] border-dashed border-gray-200 dark:border-gray-700">
                        <div class="mt-6 flex justify-between">
                            <button type="button" x-show="currentStep > 1" @click="prevStep" class="button-secondary">
                                <x-svg.arrows.left class="stroke-white"/>
                                Précédent
                            </button>
                            <button type="button" x-show="currentStep < steps.length" @click="nextStep"
                                    class="button-primary">
                                Suivant
                                <x-svg.arrows.right class="stroke-gray-700"/>
                            </button>
                            <button type="submit" x-show="currentStep === steps.length" class="button-tertiary">
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
