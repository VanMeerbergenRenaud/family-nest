<div>
    <h1 class="sr-only">Créer une facture</h1>

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
        <div class="mb-2 lg:mb-6">
            <!-- Navigation mobile -->
            <div class="lg:hidden flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-800 md:max-w-[60vw] mx-auto">

                <button
                    @click="currentStep > 1 ? prevStep() : null"
                    :class="{'opacity-50 cursor-not-allowed': currentStep <= 1, 'cursor-pointer': currentStep > 1}"
                    class="p-2"
                >
                    <x-svg.arrows.left class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                </button>

                <!-- Menu déroulant des étapes -->
                <x-menu>
                    <x-menu.button class="button-primary min-w-56 justify-center">
                        <span class="font-medium" x-text="`Étape ${currentStep} : ${steps[currentStep-1]}`"></span>
                        <x-svg.arrows.right class="ml-1 rotate-90" />
                    </x-menu.button>

                    <x-menu.items class="mt-2 w-56">
                        <template x-for="(step, index) in steps" :key="index">
                            <x-menu.item @click="goToStep(index + 1)">
                        <span class="relative w-full flex items-center">
                            <span class="w-6 h-6 rounded-full flex-center mr-2"
                                  :class="{ 'bg-slate-700 text-white': currentStep === index + 1, 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200': currentStep !== index + 1 }">
                                <span x-text="index + 1" class="text-xs"></span>
                            </span>
                            <span class="text-sm text-gray-700 dark:text-gray-200" x-text="step"></span>
                            <!-- Icône de vérification pour l'étape active -->
                            <svg x-show="currentStep === index + 1" class="h-4 w-4 absolute right-0"
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                            </x-menu.item>
                        </template>
                    </x-menu.items>
                </x-menu>

                <button
                    @click="currentStep < steps.length ? nextStep() : null"
                    :class="{'opacity-50 cursor-not-allowed': currentStep >= steps.length, 'cursor-pointer': currentStep < steps.length}"
                    class="p-2"
                >
                    <x-svg.arrows.right class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                </button>
            </div>

            <!-- Navigation desktop -->
            <div class="hidden lg:flex-center lg:flex-row lg:flex-wrap lg:px-6 lg:py-4 lg:rounded-xl lg:space-x-2">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex-center cursor-pointer whitespace-nowrap mb-4" @click="goToStep(index + 1)">
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
        </div>

        <form wire:submit.prevent="createInvoice">
            @csrf

            {{-- Invoice form --}}
            <div class="lg:px-4 max-lg:mt-4 mx-auto grid lg:grid-cols-[1fr_2fr] gap-4 lg:gap-x-10 lg:gap-y-0">

                {{-- Image : colonne 1 --}}
                <div class="relative flex-center overflow-hidden max-h-[75vh] lg:max-w-[30vw]">
                    @if (!$form->uploadedFile)
                        <x-form.field-upload label="Importer une facture" model="form.uploadedFile" name="form.uploadedFile" :asterix="true" />
                    @else
                        <div class="relative w-full h-full">
                            <!-- Button de suppression de l'image -->
                            <button type="button"
                                    wire:click="removeUploadedFile"
                                    class="absolute top-2.5 right-2.5 z-2"
                            >
                                <x-svg.cross class="text-red-600 hover:text-black bg-red-300 hover:bg-red-400 rounded-full w-6 h-6 p-1 transition-colors duration-200" />
                            </button>

                            <!-- Détection du type de fichier et affichage approprié -->
                            @php
                                $fileName = $form->uploadedFile->getClientOriginalName();
                                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']);
                                $isPdf = $fileExtension === 'pdf';
                                $isDocx = $fileExtension === 'docx';
                                $fileSize = round($form->uploadedFile->getSize() / 1024, 2); // Conversion en KB
                            @endphp

                            <div class="rounded-xl border border-slate-200 min-h-[30rem] flex flex-col items-center justify-center p-2 overflow-y-scroll">
                                <!-- Aperçu pour les images -->
                                @if ($isImage)
                                    <img src="{{ $form->uploadedFile->temporaryUrl() }}"
                                         alt="Aperçu de la facture"
                                         class="bg-gray-100 rounded-xl max-h-[50vh]"
                                    />
                                @else
                                    <!-- Icône pour les PDF -->
                                    @if ($isPdf)
                                        <div class="w-24 h-24 mb-5 flex-center bg-red-100 rounded-full">
                                            <x-svg.pdf class="w-12 h-12 text-gray-600" />
                                        </div>
                                        <!-- Icône pour les DOCX -->
                                    @elseif ($isDocx)
                                        <div class="w-24 h-24 mb-5 flex-center bg-blue-100 rounded-full">
                                            <x-svg.docx class="w-12 h-12 text-gray-600" />
                                        </div>
                                        <!-- Icône générique pour les autres types de fichiers -->
                                    @else
                                        <div class="w-24 h-24 mb-5 flex-center bg-gray-100 rounded-full">
                                            <x-svg.img class="w-12 h-12 text-gray-600" />
                                        </div>
                                    @endif
                                @endif

                                <!-- Informations sur le fichier -->
                                <div class="w-full max-w-md bg-gray-50 p-4 rounded-lg flex-center flex-col gap-2">
                                    <h3 class="text-md-medium text-gray-800 truncate">{{ $fileName }}</h3>
                                    <p class="flex-center space-x-2 text-gray-600">
                                        <span class="text-sm-regular">{{ strtoupper($fileExtension) }}</span>
                                        <span class="text-sm-regular">{{ $fileSize }} KB</span>
                                    </p>
                                    <p class="mt-3 px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 w-fit">
                                        Import du fichier validé
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Steps : colonne 2 --}}
                <div class="bg-slate-100 lg:max-w-[60vw] flex flex-col justify-between py-4 px-6 rounded-xl border border-slate-200">

                    {{-- Étape 1: Informations générales --}}
                    <x-invoice-create-step
                        step="1" title="Étape 1 : Informations générales"
                        description="Ajoutez les informations de base concernant cette facture."
                        class="grid grid-cols-1 gap-4 "
                    >
                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[3fr_2fr]">
                            <x-form.field label="Nom" name="form.name" model="form.name" placeholder="ex : Facture Internet - Octobre 2024" :asterix="true" />
                            <x-form.field label="Référence / Numéro" name="form.reference" model="form.reference" placeholder="INV-12345" />
                        </div>
                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
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

                            <x-form.field label="Fournisseur / émetteur de la facture" name="form.issuer_name"
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
                            <x-form.select name="form.payment_frequency" model="form.payment_frequency" label="Fréquence de paiement">
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
                        class="flex flex-wrap items-end gap-y-3 gap-x-4"
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


                        <x-form.select name="form.payment_method" model="form.payment_method"
                                       label="Méthode de paiement utilisée">
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
                            <label for="tags" class="relative mb-1.5 pl-2 block text-sm font-medium text-gray-800 dark:text-gray-200">
                                Tags personnalisés
                            </label>

                            <div class="flex mt-2 relative">
                                <input type="text"
                                       name="tags"
                                       id="tags"
                                       wire:model.live.debounce.300ms="form.tagInput"
                                       placeholder="Ajouter un tag..."
                                       class="flex-1 block w-full text-sm-regular rounded-l-md bg-white border border-slate-200 dark:border-gray-600 dark:text-white p-3 pl-4 focus:outline-0"
                                >
                                <button type="button" wire:click="addTag"
                                        class="inline-flex items-center px-4 py-2 text-sm-medium bg-white border border-l-0 border-slate-200 rounded-r-md hover:bg-gray-50 text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                    Ajouter un tag
                                </button>

                                <!-- Menu déroulant pour les suggestions -->
                                @if($showTagSuggestions && count($tagSuggestions) > 0)
                                    <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-10 max-h-60 overflow-y-auto dark:bg-gray-800 dark:border-gray-700">
                                        <ul class="p-1.5" x-data="{selectedIndex: -1}">
                                            @foreach($tagSuggestions as $index => $tag)
                                                <li wire:key="tag-suggestion-{{ $index }}"
                                                    x-bind:class="{'bg-indigo-50 dark:bg-indigo-900': selectedIndex === {{ $index }}}"
                                                    wire:click="selectTag('{{ $tag }}')"
                                                    class="px-4 py-2 rounded-md text-sm-regular text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900 cursor-pointer flex items-center">
                                                    <x-svg.tag class="mr-2 text-indigo-500"/>
                                                    {{ $tag }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            {{-- Tags ajoutés --}}
                            @if(count($form->tags) > 0)
                                <ul class="flex flex-wrap gap-2.5 mt-1.5 ml-2">
                                    @foreach($form->tags as $index => $tag)
                                        <li class="mt-2 inline-flex items-center pl-3.5 pr-2.5 pt-1 pb-1.5 rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                            <span class="text-sm-regular">{{ $tag }}</span>
                                            <button type="button" wire:click="removeTag({{ $index }})"
                                                    class="relative top-0.25 ml-1.5 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">
                                                <x-svg.cross class="h-4 w-4 text-indigo-700" />
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </x-invoice-create-step>

                    <x-invoice-create-step
                        step="7" title="Étape 7 : Résumé"
                        description="Vérifiez les informations avant d'enregistrer la facture."
                    >
                        <!-- Résumé du formulaire -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                            <dl>
                                <x-invoices.summary-item label="Nom de la facture" :alternateBackground="true">
                                    {{ $form->name ?: 'Non spécifié' }}
                                </x-invoices.summary-item>

                                <x-invoices.summary-item label="Type et catégorie">
                                    {{ $form->type ?: 'Non spécifié' }} - {{ $form->category ?: 'Non spécifié' }}
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
                                            {{ $form->payment_reminder ? \Carbon\Carbon::parse($form->payment_reminder)->format('d/m/Y') : 'Non spécifié' }}
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
                                        @forelse($form->tags as $tag)
                                            <span class="px-2 py-1 rounded-full text-xs-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                                {{ $tag }}
                                            </span>
                                        @empty
                                            Aucun tag
                                        @endforelse
                                    </div>
                                </x-invoices.summary-item>

                                <x-invoices.summary-item label="Pièce jointe" :alternateBackground="true">
                                    @if($form->uploadedFile)
                                        @php
                                            $fileName = $form->uploadedFile->getClientOriginalName();
                                            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                            $fileSize = round($form->uploadedFile->getSize() / 1024, 2); // Conversion en KB
                                        @endphp
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="h-5 w-5 mr-2 text-green-500" viewBox="0 0 20 20"
                                                 fill="currentColor">
                                                <path fill-rule="evenodd"
                                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <span class="text-sm-medium">{{ $fileName }}</span>
                                                <span class="text-xs text-gray-500 ml-2">({{ strtoupper($fileExtension) }}, {{ $fileSize }} KB)</span>
                                            </div>
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
                    <div class="mt-6 border-t-[0.1rem] w-full border-dashed border-gray-200 dark:border-gray-700">
                        <div class="mt-4 flex flex-wrap justify-between gap-4">
                            <button type="button" x-show="currentStep > 1" @click="prevStep" class="button-secondary">
                                <x-svg.arrows.left class="stroke-white"/>
                                Précédent
                            </button>
                            <button type="button" x-show="currentStep < steps.length" @click="nextStep" class="ml-auto button-primary">
                                Suivant
                                <x-svg.arrows.right class="stroke-gray-700"/>
                            </button>
                            <button type="submit" x-show="currentStep < steps.length" class="button-tertiary">
                                Tout valider
                            </button>
                            <button type="submit" x-show="currentStep === steps.length" class="button-tertiary">
                                Valider
                            </button>
                        </div>
                    </div>
                    {{-- Fin colonne 2 --}}
                </div>
            </div>

            {{-- Gestions des messages d'erreurs --}}
            <div class="my-6 lg:px-4 mx-auto max-lg:max-w-[35rem] max-lg:flex-center max-lg:flex-col gap-4 lg:grid lg:grid-cols-[1fr_2fr] lg:gap-x-10 lg:gap-y-0">
                <!-- Affichage des erreurs de validation -->
                @if($errors->any())
                    <x-form.alert type="warning" title="Attention : corrections requises">
                        <p class="text-sm-regular">
                            Veuillez corriger toutes les erreurs avant de soumettre le formulaire.
                            Vous pouvez naviguer vers les étapes précédentes pour effectuer les corrections nécessaires.
                        </p>

                        @php
                            // Calculer le pourcentage de progression même en cas d'erreur
                            $requiredFields = ['uploadedFile', 'name', 'amount'];
                            $filledFields = 0;

                            if (!empty($form->uploadedFile) || !empty($form->existingFilePath)) $filledFields++;
                            if (!empty($form->name)) $filledFields++;
                            if (isset($form->amount) && $form->amount !== '') $filledFields++;

                            $progressPercentage = round(($filledFields / count($requiredFields)) * 100);
                        @endphp

                        <div class="mt-4">
                            <p class="flex justify-between mb-1">
                                <span class="text-xs-medium">Progression du formulaire</span>
                                <span class="text-xs-medium">{{ $progressPercentage }}%</span>
                            </p>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-orange-400 h-2.5 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                            </div>
                        </div>
                        <p class="text-xs mt-2">
                            Veuillez corriger les erreurs ci-contre avant de tout valider.
                        </p>
                    </x-form.alert>

                    <x-form.alert type="error" title="Veuillez corriger les erreurs suivantes avant de continuer :" layout="header">
                        <div class="flex flex-col gap-2">
                            @php
                                $fieldToStep = [
                                    'fichier' => 1,
                                    'nom' => 1,
                                    'référence' => 1,
                                    'type' => 1,
                                    'catégorie' => 1,
                                    'fournisseur' => 1,
                                    'site internet' => 1,
                                    'montant' => 2,
                                    'devise' => 2,
                                    'payée par' => 2,
                                    'date d\'émission' => 3,
                                    'date de paiement' => 3,
                                    'rappel de paiement' => 3,
                                    'fréquence' => 3,
                                    'engagement' => 4,
                                    'statut' => 5,
                                    'méthode de paiement' => 5,
                                    'priorité' => 5,
                                    'notes' => 6,
                                    'tags' => 6,
                                ];

                                $errorsByStep = [];

                                // Grouper les erreurs par étape
                                foreach ($errors->all() as $error) {
                                    $step = 1;
                                    foreach ($fieldToStep as $field => $fieldStep) {
                                        if (stripos($error, $field) !== false) {
                                            $step = $fieldStep;
                                            break;
                                        }
                                    }
                                    if (!isset($errorsByStep[$step])) {
                                        $errorsByStep[$step] = [];
                                    }
                                    $errorsByStep[$step][] = $error;
                                }

                                // Trier par numéro d'étape
                                ksort($errorsByStep);
                            @endphp

                            @foreach ($errorsByStep as $step => $stepErrors)
                                <div class="flex items-center justify-between border-b border-red-100">
                                    <p class="pl-2 text-sm-bold text-red-600">Étape {{ $step }}</p>
                                    <button type="button" @click="goToStep({{ $step }})" class="button-classic text-red-600 pr-3 py-1.5 hover:bg-red-100">
                                        Aller à cette étape
                                        <x-svg.arrows.right class="text-red-600" />
                                    </button>
                                </div>
                                <ul class="pl-5 space-y-2 list-disc">
                                    @foreach ($stepErrors as $error)
                                        <li class="text-sm-regular text-red-600">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endforeach
                        </div>
                    </x-form.alert>
                {{-- Pas d'image importé --}}
                @elseif(empty($form->uploadedFile) && empty($form->existingFilePath))
                    <x-form.alert type="warning" title="Aucune facture importée">
                        <p class="text-sm-regular">
                            Veuillez importer une facture pour commencer.
                        </p>

                        <div class="mt-4">
                            <div class="flex justify-between mb-1">
                                <span class="text-xs-medium">Progression du formulaire</span>
                                <span class="text-xs-medium">0%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: 0"></div>
                            </div>
                            <p class="text-xs mt-2">Commencez par importez votre facture pour continuer votre progression</p>
                        </div>
                    </x-form.alert>
                {{-- Image importé mais champs obligatoires vides --}}
                @else
                    @php
                        // Calcul dynamique du pourcentage de progression
                        $requiredFields = ['uploadedFile', 'name', 'amount'];
                        $optionalFields = ['type', 'category', 'issuer_name', 'issuer_website', 'paid_by','associated_members','issued_date','payment_due_date','payment_reminder','payment_frequency','payment_status','payment_method','priority','notes','tags'];

                        $filledRequired = 0;
                        $filledOptional = 0;

                        // Vérifier les champs obligatoires
                        if (!empty($form->uploadedFile)) $filledRequired++;
                        if (!empty($form->name)) $filledRequired++;
                        if (isset($form->amount) && $form->amount !== '') $filledRequired++;

                        // Vérifier les champs optionnels pour bonus de progression
                        if (!empty($form->type)) $filledOptional++;
                        if (!empty($form->category)) $filledOptional++;
                        if (!empty($form->issuer_name)) $filledOptional++;
                        if (!empty($form->issuer_website)) $filledOptional++;
                        if (!empty($form->paid_by)) $filledOptional++;
                        if (!empty($form->associated_members)) $filledOptional++;
                        if (!empty($form->issued_date)) $filledOptional++;
                        if (!empty($form->payment_due_date)) $filledOptional++;
                        if (!empty($form->payment_reminder)) $filledOptional++;
                        if (!empty($form->payment_frequency)) $filledOptional++;
                        if (!empty($form->payment_status)) $filledOptional++;
                        if (!empty($form->payment_method)) $filledOptional++;
                        if (!empty($form->priority)) $filledOptional++;
                        if (!empty($form->notes)) $filledOptional++;
                        if (!empty($form->tags)) $filledOptional++;

                        // Calcul du pourcentage (70% pour les champs obligatoires + 30% pour les champs optionnels)
                        $requiredPercentage = ($filledRequired / count($requiredFields)) * 70;
                        $optionalPercentage = ($filledOptional / count($optionalFields)) * 30;
                        $totalPercentage = round($requiredPercentage + $optionalPercentage);

                        // Garantir que le pourcentage est entre 0 et 100
                        $totalPercentage = max(0, min(100, $totalPercentage));

                        // Déterminer le type d'alerte et la couleur en fonction du pourcentage
                        $alertType = $totalPercentage == 100 ? 'success' : 'inProgress';
                        $barColor = $totalPercentage == 100 ? 'bg-green-600' : 'bg-orange-500';
                        $alertTitle = $totalPercentage == 100 ? 'Prêt à soumettre' : 'Champs obligatoires non remplis';
                    @endphp

                    <x-form.alert type="{{ $alertType }}" title="{{ $alertTitle }}">
                        <p class="text-sm-regular">
                            @if($totalPercentage == 100)
                                Toutes les informations requises sont complètes. Vous pouvez maintenant enregistrer cette facture si vous le souhaitez.
                            @else
                                Veuillez remplir tous les champs obligatoires pour enregistrer votre facture.
                            @endif
                        </p>

                        <div class="mt-4">
                            <div class="flex justify-between mb-1">
                                <span class="text-xs-medium">Progression du formulaire</span>
                                <span class="text-xs-medium">{{ $totalPercentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="{{ $barColor }} h-2.5 rounded-full" style="width: {{ $totalPercentage }}%"></div>
                            </div>
                            <p class="text-xs mt-2">
                                @if($totalPercentage == 100)
                                    Félicitations ! Vous avez complété tous les champs nécessaires.
                                @elseif($totalPercentage >= 70)
                                    Vous avez rempli tous les champs obligatoires ! Complétez les champs restants pour une meilleure expérience.
                                @elseif($totalPercentage > 0)
                                    Continuez en remplissant les champs obligatoires suivis d'un astérisque *.
                                @endif
                            </p>
                        </div>
                    </x-form.alert>
                @endif
            </div>
        </form>
    </div>
</div>
