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

        <form wire:submit.prevent="createInvoice">
            @csrf

            {{-- Facture preview--}}
            <div class="max-lg:flex-center gap-4 lg:grid lg:grid-cols-[30vw_auto] lg:gap-12">

                {{-- Image : colonne 1 --}}
                <div class="max-lg:hidden overflow-hidden flex-center max-h-[75vh] max-w-[30vw] relative">
                    @if (!$uploadedFile)
                        <x-form.field-upload label="Importer une facture" model="uploadedFile" name="uploadedFile"/>
                    @else
                        <div class="relative w-full h-full">
                            <!-- Button de suppression de l'image -->
                            <button type="button"
                                    wire:click="removeUploadedFile"
                                    class="absolute top-3 right-3 z-2 bg-red-400 hover:bg-red-500 text-gray-200 rounded-full p-1 shadow-md transition-colors duration-200"
                            >
                                <x-svg.cross/>
                            </button>
                            <!-- Aperçu de l'image -->
                            <img src="{{ $uploadedFile->temporaryUrl() }}"
                                 alt="Image temporaire de la preview de la facture"
                                 class="rounded-2xl h-full min-h-[30rem]"
                            />
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
                        <x-form.field label="Nom *" name="name" model="name"
                                      placeholder="ex : Facture Internet - Octobre 2024"/>

                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 ">

                            <x-form.select label="Type*" name="type" model="type" label="Type">
                                <option value="" selected>Sélectionner un type</option>
                                @foreach($invoiceTypes as $typeValue => $typeLabel)
                                    <option value="{{ $typeValue }}">{{ $typeLabel }}</option>
                                @endforeach
                            </x-form.select>

                            <x-form.select label="Catégorie*" name="category" model="category" label="Catégorie">
                                <option value="" selected>Sélectionner une catégorie</option>
                                @foreach($availableCategories as $availableCategory)
                                    <option value="{{ $availableCategory }}">{{ $availableCategory }}</option>
                                @endforeach
                            </x-form.select>

                            <x-form.field label="Fournisseur / émetteur de la facture*" name="issuer_name"
                                          model="issuer_name" placeholder="Nom du fournisseur"/>
                            <x-form.field label="Site internet du fournisseur *" name="issuer_website"
                                          model="issuer_website" placeholder="www.monfournisseur.com"/>
                        </div>

                    </x-invoice-create-step>

                    {{-- Étape 2: Détails financiers --}}
                    <x-invoice-create-step
                        step="2" title="Étape 2 : Détails financiers"
                        description="Choisissez le type de facture que vous venez d'importer."
                        class="grid grid-cols-1 lg:grid-cols-2 gap-4"
                    >
                        <x-form.field label="Montant total à payer *" name="amount" model="amount" type="number"
                                      step="1"/>

                        <x-form.select name="paid_by" model="paid_by" wire:change="calculateDistribution"
                                       label="Qui paye la facture *">
                            <option value="0" disabled>Sélectionner la personne</option>
                            @foreach($family_members as $member)
                                <option value="{{ $member }}">{{ $member }}</option>
                            @endforeach
                        </x-form.select>

                        <x-form.select name="associated_members" model="associated_members"
                                       wire:change="calculateDistribution"
                                       label="Associé aux membres de la famille">
                            <option value="">Sélectionner...</option>
                            @foreach($family_members as $member)
                                <option value="{{ $member }}">{{ $member }}</option>
                            @endforeach
                        </x-form.select>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Répartition du
                                montant en fonction des membres sélectionnés</label>
                            @if(!empty($amount_distribution))
                                @foreach($amount_distribution as $member => $percentage)
                                    <div class="mt-2">
                                        <label>{{ $member }} : {{ $percentage }}%
                                            ({{ number_format(($percentage / 100) * $amount, 2) }} €)</label>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                            <div class="bg-purple-600 h-2.5 rounded-full"
                                                 style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <input type="range"
                                               min="0"
                                               max="100"
                                               step="5"
                                               value="{{ $percentage }}"
                                               wire:change="adjustDistribution('{{ $member }}', $event.target.value)"
                                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                                    </div>
                                @endforeach
                            @else
                                <p class="text-gray-500">Veuillez sélectionner les membres concernés par cette
                                    facture.</p>
                            @endif
                        </div>
                    </x-invoice-create-step>

                    {{-- Étape 3: Dates importantes --}}
                    <x-invoice-create-step
                        step="3" title="Étape 3 : Dates importantes"
                        description="Indiquez les dates importantes concernant cette facture."
                    >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Date d'émission -->
                            <div class="space-y-2">
                                <label for="issued_date"
                                       class="text-sm font-medium text-slate-700 dark:text-slate-300 flex items-center gap-1">
                                    Date d'émission
                                    <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">

                                    <input
                                        type="date"
                                        id="issued_date"
                                        wire:model="issued_date"
                                        class="w-full pl-3 pr-2 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-700 dark:text-slate-200"
                                    >
                                </div>
                            </div>

                            <!-- Date de paiement -->
                            <div class="space-y-2">
                                <label for="payment_due_date"
                                       class="text-sm font-medium text-slate-700 dark:text-slate-300 flex items-center gap-1">
                                    Date de paiement
                                    <span class="text-rose-500">*</span>
                                    <span class="ml-auto">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                             class="w-4 h-4 text-slate-400 hover:text-slate-600 cursor-help"
                                             x-tooltip="Date limite à laquelle le paiement doit être effectué">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                        </svg>
                                    </span>
                                </label>
                                <div class="relative">

                                    <input
                                        type="date"
                                        id="payment_due_date"
                                        wire:model="payment_due_date"
                                        class="w-full pl-3 pr-2 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-700 dark:text-slate-200"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Rappel de paiement -->
                        <div class="space-y-2">
                            <label for="payment_reminder"
                                   class="text-sm font-medium text-slate-700 dark:text-slate-300 flex items-center gap-1">
                                Rappel de paiement
                            </label>
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="w-5 h-5">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    id="payment_reminder"
                                    wire:model="payment_reminder"
                                    placeholder="15/12/2024 + 5 jours à l'avance"
                                    class="w-full pl-10 pr-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-700 dark:text-slate-200 placeholder-slate-400"
                                >
                            </div>
                        </div>

                        <x-form.select name="payment_frequency" model="payment_frequency" label="Fréquence de paiement">
                            <option value="">Sélectionner...</option>
                            <option value="monthly">Mensuel</option>
                            <option value="quarterly">Trimestriel</option>
                            <option value="annually">Annuel</option>
                            <option value="one_time">Ponctuel</option>
                        </x-form.select>
                    </x-invoice-create-step>

                    {{-- Étape 4: Association à des engagements --}}
                    <x-invoice-create-step
                        step="4" title="Étape 4 : Association à des engagements"
                        description="Associez cette facture à un engagement existant ou créez-en un nouveau."
                        class="grid grid-cols-1 lg:grid-cols-2 gap-4"
                    >

                        <x-form.field name="engagement_name" model="engagement_name"
                                      placeholder="Nom de l'engagement" label="Nom de l'engagement"/>

                        <div>
                            <button type="button"
                                    class="w-full px-4 py-2 bg-blue-900 text-white">
                                Créer un nouvel engagement
                            </button>
                        </div>
                    </x-invoice-create-step>

                    {{-- Étape 5: Statut de paiement --}}
                    <x-invoice-create-step
                        step="5" title="Étape 5 : Statut de paiement"
                        description="Indiquez le statut actuel de paiement de cette facture."
                        class="grid grid-cols-1 lg:grid-cols-2 gap-4"
                    >
                        <x-form.select name="payment_status" model="payment_status" label="Statut de la facture *">
                            <option value="unpaid">Non payée</option>
                            <option value="paid">Payée</option>
                            <option value="late">En retard</option>
                            <option value="partially_paid">Partiellement payée</option>
                        </x-form.select>


                        <x-form.select name="payment_method" model="payment_method"
                                       label="Méthode de paiement utilisée">
                            <option value="card">Carte bancaire</option>
                            <option value="cash">Espèces</option>
                            <option value="transfer">Virement</option>
                        </x-form.select>

                        <x-form.select name="priority" model="priority" label="Étiquette de priorité">
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

                        <x-form.field-textarea label="Notes (détail / commentaire important)" name="notes" model="notes"
                                               placeholder="Inscrivez votre message ici..." class="border-gray"/>
                        <div class="text-right text-xs text-gray-500 mt-1">
                            Montant de caractère maximum <span>{{ strlen($notes) }}</span>/500
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tags personnalisés</label>
                            <div class="flex flex-wrap gap-2 mb-2">
                                @foreach($tags as $index => $tag)
                                    <div
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                        {{ $tag }}
                                        <button type="button" wire:click="removeTag({{ $index }})"
                                                class="ml-2 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">
                                            <x-svg.cross class="h-4 w-4"/>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex mt-2">
                                <input type="text" wire:model="tagInput" wire:keydown.enter.prevent="addTag"
                                       class="flex-1 block w-full focus:outline-0"
                                       placeholder="Ajouter un tag...">
                                <button type="button" wire:click="addTag"
                                        class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                    Ajouter un tag
                                </button>
                            </div>
                        </div>
                    </x-invoice-create-step>

                    <x-invoice-create-step
                        step="7" title="Étape 7 : Résumé"
                        description="Vérifiez les informations avant d'enregistrer la facture."
                    >
                        <!-- Affichage des erreurs de validation -->
                        @if ($errors->any())
                            <x-form.alert type="warning" title="Attention : corrections requises">
                                Veuillez corriger toutes les erreurs avant de soumettre le formulaire.
                                Vous pouvez naviguer vers les étapes précédentes pour effectuer les corrections
                                nécessaires.
                            </x-form.alert>

                            <x-form.alert type="error"
                                          title="Veuillez corriger les erreurs suivantes avant de continuer :">
                                <ul class="space-y-1 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>

                                <x-slot name="actions">
                                    <button type="button"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                            @click="goToStep(1)">
                                        Retourner au début du formulaire
                                    </button>
                                </x-slot>
                            </x-form.alert>
                        @else
                            <x-form.alert type="success" title="Prêt à soumettre">
                                Toutes les informations sont complètes. Vous pouvez maintenant enregistrer cette
                                facture.
                            </x-form.alert>
                        @endif

                        <!-- Résumé du formulaire -->
                        <div
                            class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                            <dl>
                                <div
                                    class="bg-gray-50 dark:bg-gray-900 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Nom de la facture
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        {{ $name ?: 'Non spécifié' }}
                                        @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </dd>
                                </div>

                                <div
                                    class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Type et catégorie
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        {{ $type ?: 'Non spécifié' }} - {{ $category ?: 'Non spécifié' }}
                                        @error('type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        @error('category')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </dd>
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-900 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Fournisseur</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        {{ $issuer_name ?: 'Non spécifié' }}
                                        @if($issuer_website)
                                            <a href="{{ $issuer_website }}" target="_blank"
                                               class="text-indigo-500 hover:text-indigo-700 ml-2">({{ $issuer_website }}
                                                )</a>
                                        @endif
                                        @error('issuer_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        @error('issuer_website')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </dd>
                                </div>

                                <div
                                    class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Montant</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        {{ $amount ? number_format($amount, 2) . ' €' : 'Non spécifié' }}
                                        @error('amount')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </dd>
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-900 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Répartition du
                                        montant
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        @if(!empty($amount_distribution) && $amount)
                                            <ul class="space-y-2">
                                                @foreach($amount_distribution as $member => $percentage)
                                                    <li>
                                                        {{ $member }}: {{ $percentage }}%
                                                        ({{ number_format(($percentage / 100) * $amount, 2) }} €)
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            Non spécifié
                                        @endif
                                        @error('amount_distribution')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        @error('paid_by')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        @error('associated_members')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </dd>
                                </div>

                                <div
                                    class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Dates</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        <p>Date
                                            d'émission: {{ $issued_date ? \Carbon\Carbon::parse($issued_date)->format('d/m/Y') : 'Non spécifiée' }}</p>
                                        <p>Date de
                                            paiement: {{ $payment_due_date ? \Carbon\Carbon::parse($payment_due_date)->format('d/m/Y') : 'Non spécifiée' }}</p>
                                        <p>Rappel: {{ $payment_reminder ?: 'Non spécifié' }}</p>
                                        <p>Fréquence: {{ $payment_frequency ?: 'Non spécifiée' }}</p>
                                        @error('issued_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        @error('payment_due_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </dd>
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-900 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Engagement</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        <p>ID: {{ $engagement_id ?: 'Non spécifié' }}</p>
                                        <p>Nom: {{ $engagement_name ?: 'Non spécifié' }}</p>
                                        @error('engagement_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        @error('engagement_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </dd>
                                </div>

                                <div
                                    class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Statut de
                                        paiement
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                          {{ $payment_status === 'paid' ? 'bg-green-100 text-green-800' :
                           ($payment_status === 'late' ? 'bg-red-100 text-red-800' :
                           ($payment_status === 'partially_paid' ? 'bg-yellow-100 text-yellow-800' :
                            'bg-gray-100 text-gray-800')) }}">
                        {{ $payment_status === 'paid' ? 'Payée' :
                           ($payment_status === 'late' ? 'En retard' :
                           ($payment_status === 'partially_paid' ? 'Partiellement payée' : 'Non payée')) }}
                    </span>
                                        <p class="mt-1">Méthode: {{ $payment_method ?
                        ($payment_method === 'card' ? 'Carte bancaire' :
                        ($payment_method === 'cash' ? 'Espèces' : 'Virement')) : 'Non spécifiée' }}</p>
                                        <p>Priorité: {{ $priority ?
                        ($priority === 'high' ? 'Élevée' :
                        ($priority === 'medium' ? 'Moyenne' :
                        ($priority === 'low' ? 'Basse' : 'Aucune'))) : 'Non spécifiée' }}</p>
                                        <p>Archivée: {{ $is_archived ? 'Oui' : 'Non' }}</p>
                                        @error('payment_status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        @error('payment_method')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        @error('priority')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </dd>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Notes</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        {{ $notes ?: 'Aucune note' }}
                                        @error('notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </dd>
                                </div>

                                <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Tags</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        <div class="flex flex-wrap gap-2">
                                            @forelse($tags as $tag)
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                    {{ $tag }}
                                                </span>
                                            @empty
                                                Aucun tag
                                            @endforelse
                                        </div>
                                        @error('tags')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </dd>
                                </div>

                                <div
                                    class="bg-gray-50 dark:bg-gray-900 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Pièce jointe</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        @if($uploadedFile)
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
                                        @error('uploadedFile')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </x-invoice-create-step>

                    {{-- Boutons de Navigation --}}
                    <div class="my-6 border-t-[0.1rem] border-dashed border-gray-200 dark:border-gray-700">
                        <div class="mt-6 flex justify-between">
                            <button type="button" x-show="currentStep > 1" @click="prevStep"
                                    class="mr-4 inline-flex items-center text-sm-medium rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="mr-2 -ml-1 h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                                </svg>
                                Précédent
                            </button>
                            <button type="button" x-show="currentStep < steps.length" @click="nextStep"
                                    class="inline-flex items-center text-sm-medium rounded-lg border border-blue-900 bg-blue-900 px-4 py-2 text-white hover:bg-blue-800 dark:bg-blue-900 dark:border-blue-900 dark:hover:bg-blue-800">
                                Suivant
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="ml-2 -mr-1 h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                </svg>
                            </button>
                            <button type="submit" x-show="currentStep === steps.length"
                                    class="inline-flex items-center text-sm-medium rounded-lg bg-purple-500 px-4 py-2 text-white hover:bg-purple-600 dark:bg-purple-600 dark:hover:bg-purple-700">
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
