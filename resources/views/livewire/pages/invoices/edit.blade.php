<div>
    @if($isEditMode)
        <x-slot name="banner">
            <x-banner message="Mode édition en cours de la facture" />
        </x-slot>
    @endif

    <h2 role="heading" aria-level="2" class="sr-only">Modifier une facture</h2>

    {{-- Formulaire pour modifier une facture : multi step  --}}
    <div x-data="{
        currentStep: 1,
        steps: ['Informations', 'Montant', 'Dates', 'Paiement', 'Notes', 'Résumé'],
        nextStep() {
            this.currentStep++;
        },
        prevStep() {
            this.currentStep--;
        },
        goToStep(step) {
            this.currentStep = step;
        }
    }" class="mx-auto md:max-w-[45rem] lg:max-w-[70rem]">

        {{-- Barre de progression avec les étapes --}}
        <x-invoices.create.nav-step />

        <form wire:submit.prevent="updateInvoice">
            @csrf

            {{-- Invoice form --}}
            <div class="lg:px-4 max-lg:mt-4 mx-auto grid lg:grid-cols-[1fr_2fr] gap-4 lg:gap-x-10 lg:gap-y-0">

                {{-- Image : colonne 1 --}}
                <div class="relative flex-center overflow-hidden max-h-[75vh] lg:max-w-[30vw]">
                    @if ($form->existingFilePath && !$form->uploadedFile)
                        {{-- Affichage du fichier existant --}}
                        <div class="relative w-full h-full">
                            <!-- Button de suppression de l'image -->
                            <button type="button"
                                    wire:click="removeUploadedFile"
                                    class="absolute top-2.5 right-2.5 z-10">
                                <x-svg.cross class="text-red-600 hover:text-black bg-red-300 hover:bg-red-400 rounded-full w-6 h-6 p-1 transition-colors duration-200" />
                            </button>

                            <div class="rounded-xl border border-slate-200 min-h-[30rem] flex flex-col items-center justify-center p-2 overflow-y-auto">
                                <x-file-viewer
                                    :filePath="$filePath"
                                    :fileExtension="$fileExtension"
                                    :fileName="$fileName"
                                    class="w-full h-[40vh] mb-4"
                                />

                                <!-- Informations sur le fichier -->
                                <div class="w-full max-w-md bg-gray-50 p-4 rounded-lg flex-center flex-col gap-2">
                                    <h3 role="heading" aria-level="3" class="text-md-medium text-gray-800 truncate">
                                        <span class="sr-only">Fichier :</span>{{ $form->fileName }}
                                    </h3>
                                    <p class="flex-center text-sm-regular text-gray-600">
                                        {{ strtoupper($form->fileExtension) }}
                                    </p>

                                    <p class="mt-2 px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 w-fit">
                                        Fichier existant
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif ($form->uploadedFile)
                        {{-- Affichage du nouveau fichier uploadé --}}
                        <div class="relative w-full h-full">
                            <!-- Button de suppression de l'image -->
                            <button type="button"
                                    wire:click="removeUploadedFile"
                                    class="absolute top-2.5 right-2.5 z-10">
                                <x-svg.cross class="text-red-600 hover:text-black bg-red-300 hover:bg-red-400 rounded-full w-6 h-6 p-1 transition-colors duration-200" />
                            </button>

                            @php
                                $fileInfo = app(App\Services\FileStorageService::class)->getFileInfo($form->uploadedFile);
                                $fileInfo['status'] = !$errors->has('form.uploadedFile') ? 'success' : 'error';
                                $fileInfo['statusMessage'] = !$errors->has('form.uploadedFile') ? 'Nouveau fichier à importer' : 'Erreur lors de l\'import du fichier';
                            @endphp

                            <div class="rounded-xl border border-slate-200 min-h-[30rem] flex-center flex-col p-2 overflow-y-auto">
                                <!-- Aperçu pour les images -->
                                @if ($fileInfo['isImage'] ?? false)
                                    <img src="{{ $form->uploadedFile->temporaryUrl() }}"
                                         alt="Aperçu de la facture"
                                         class="bg-gray-100 rounded-xl max-h-[40vh] mb-4"
                                    />
                                @elseif ($fileInfo['isPdf'] ?? false)
                                    <div class="p-5 text-gray-700 text-md-medium border border-slate-200 rounded-xl bg-slate-100 mb-4">
                                        <p class="mb-2.5 text-center font-medium text-slate-700">Aperçu disponible après enregistrement</p>
                                        <p class="text-sm text-center text-slate-500">Le fichier PDF sera affiché après enregistrement.</p>
                                    </div>
                                @elseif ($fileInfo['isDocx'] ?? false)
                                    <div class="p-5 text-gray-700 text-md-medium border border-slate-200 rounded-xl bg-slate-100 mb-4">
                                        <p class="mb-2.5 text-center font-medium text-slate-700">Aperçu non disponible pour les fichiers Word</p>
                                        <p class="text-sm text-center text-slate-500">Le fichier sera disponible après enregistrement.</p>
                                    </div>
                                @elseif($fileInfo['isCsv'] ?? false)
                                    <div class="w-24 h-24 mb-5 flex-center bg-green-100 rounded-full">
                                        <x-svg.csv class="w-12 h-12 text-gray-600" />
                                    </div>
                                @else
                                    <div class="w-24 h-24 mb-5 flex-center bg-gray-100 rounded-full">
                                        <x-svg.img class="w-12 h-12 text-gray-600" />
                                    </div>
                                @endif

                                <!-- Informations sur le fichier -->
                                <div class="w-full max-w-md bg-gray-50 p-4 rounded-lg flex-center flex-col gap-2">
                                    <h2 class="text-md-medium text-gray-800 truncate">{{ $fileInfo['name'] ?? '' }}</h2>
                                    <p class="flex-center space-x-1.5 text-gray-600">
                                        <span class="text-sm-regular">{{ strtoupper($fileInfo['extension'] ?? '') }}</span>
                                        <span class="text-sm-regular">{{ $fileInfo['sizeFormatted'] ?? '' }}</span>
                                    </p>

                                    <p class="mt-2 px-3 py-1 text-xs rounded-full {{ ($fileInfo['status'] ?? '') === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} w-fit">
                                        {{ $fileInfo['statusMessage'] ?? 'Nouveau fichier' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Champ d'upload vide --}}
                        <x-form.field-upload
                            label="Importer une facture"
                            model="form.uploadedFile"
                            name="form.uploadedFile"
                            :asterix="true"
                            title="Importation en cours..."
                        />
                    @endif
                </div>

                {{-- Steps : colonne 2 --}}
                <div class="relative bg-slate-100 lg:max-w-[60vw] flex flex-col justify-between py-4 px-6 rounded-xl border border-slate-200">

                    {{-- Étape 1: Informations générales --}}
                    <x-invoices.create.form-step
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
                                    <option value="{{ $typeValue }}">{!! $typeLabel !!}</option>
                                @endforeach
                            </x-form.select>

                            {{-- Section pour la catégorie --}}
                            <x-form.select label="Catégorie*" name="form.category" model="form.category" label="Catégorie">
                                <option value="" selected>Sélectionner une catégorie</option>
                                @foreach($form->availableCategories as $categoryValue => $categoryLabel)
                                    <option value="{{ $categoryValue }}">{!! $categoryLabel !!}</option>
                                @endforeach
                            </x-form.select>

                            <x-form.field label="Fournisseur / émetteur de la facture" name="form.issuer_name" model="form.issuer_name" placeholder="Nom du fournisseur"/>
                            <x-form.field label="Site internet du fournisseur" name="form.issuer_website" model="form.issuer_website" placeholder="https://monfournisseur.com"/>
                        </div>
                    </x-invoices.create.form-step>

                    {{-- Étape 2: Détails financiers --}}
                    <x-invoices.create.form-step
                        step="2" title="Étape 2 : Détails financiers"
                        description="Choisissez le montant et qui paie cette facture."
                        class="grid grid-cols-1 gap-4"
                    >
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <x-form.field-currency
                                label="Montant total à payer"
                                name="form.amount"
                                model="form.amount"
                                :initialValue="$form->amount"
                                defaultCurrency="{{ $form->currency }}"
                                placeholder="0,00"
                                asterix="true"
                            />

                            <x-form.select name="form.paid_by_user_id" model="form.paid_by_user_id" label="Qui paie cette facture" asterix="true">
                                <option value="" disabled>Sélectionner une personne</option>
                                @foreach($family_members as $member)
                                    <option value="{{ $member->id }}">
                                        {{ $member->name }}
                                        @if($member->id === auth()->id())
                                            (Moi)
                                        @endif
                                    </option>
                                @endforeach
                            </x-form.select>
                        </div>

                        {{-- Section de répartition avec devise dynamique --}}
                        @if($form->amount > 0 && $form->paid_by_user_id)
                            <div x-data="{ showShareInterface: false }">
                                <button
                                    type="button"
                                    @click="showShareInterface = !showShareInterface"
                                    class="button-primary w-full justify-between px-3.5 py-2.5"
                                >
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                        </svg>
                                        <span class="text-sm-medium">Répartir entre les membres</span>
                                    </div>

                                    <div class="flex items-center">
                                        @php $shareSummary = $this->getShareSummary(); @endphp
                                        @if($shareSummary['totalShares'] > 0)
                                            <span class="mr-3 px-2 py-0.5 text-xs font-medium rounded-full {{ $shareSummary['isComplete'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $shareSummary['totalShares'] }} {{ $shareSummary['totalShares'] === 1 ? 'membre' : 'membres' }} •
                                                {{ $shareSummary['formattedTotalPercent'] }}%
                                            </span>
                                        @endif

                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 transition-transform" :class="{'rotate-180': showShareInterface}" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>

                                <div x-show="showShareInterface" x-cloak class="mt-3 p-3 border border-gray-200 rounded-md">
                                    <div class="flex justify-between items-center mb-3">
                                        <div class="flex items-center space-x-3">
                                            <button type="button"
                                                    wire:click="distributeEvenly()"
                                                    class="px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-md transition text-xs">
                                                Partager équitablement
                                            </button>

                                            <div class="flex items-center space-x-1">
                                                <button type="button"
                                                        wire:click="$set('shareMode', 'percentage')"
                                                        class="w-7 h-7 flex-center text-xs rounded-md {{ $shareMode === 'percentage' ? 'bg-indigo-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                                                    %
                                                </button>
                                                <button type="button"
                                                        wire:click="$set('shareMode', 'amount')"
                                                        class="w-7 h-7 flex-center text-xs rounded-md {{ $shareMode === 'amount' ? 'bg-indigo-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                                                    {{ $this->getCurrencySymbol() }}
                                                </button>
                                            </div>
                                        </div>

                                        <div class="ml-2 text-xs text-right px-2 py-1 rounded-md {{ $remainingPercentage <= 0.1 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            @if($shareMode === 'percentage')
                                                {{ \Illuminate\Support\Number::format($remainingPercentage, 1, locale: 'fr_FR') }}% non attribués
                                            @else
                                                {{ \Illuminate\Support\Number::currency($remainingAmount, $form->currency ?? 'EUR', locale: 'fr_FR') }} non attribués
                                            @endif
                                        </div>
                                    </div>

                                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-3">
                                        @foreach($family_members as $member)
                                            @php $memberShare = $this->getMemberShareInfo($member->id); @endphp
                                            <li class="flex items-center p-2 border {{ $memberShare['hasShare'] ? 'border-indigo-200 bg-indigo-50' : 'border-gray-200 bg-white' }} rounded-md">
                                                <div class="w-6 h-6 bg-indigo-100 text-indigo-700 rounded-full flex-center text-xs">
                                                    <img src="{{ $member->avatar_url ?? asset('img/img_placeholder.jpg') }}" alt="{{ $member->name }}" class="w-6 h-6 rounded-full">
                                                </div>
                                                <span class="text-xs font-medium ml-2 mr-auto truncate max-w-[80px]">{{ $member->name }}</span>

                                                @if($memberShare['hasShare'])
                                                    <div class="flex items-center">
                                                        <input
                                                            type="number"
                                                            step="0.01"
                                                            min="0"
                                                            max="{{ $shareMode === 'percentage' ? 100 : $form->amount }}"
                                                            wire:model="form.user_shares.{{ $memberShare['shareIndex'] }}.{{ $shareMode === 'percentage' ? 'percentage' : 'amount' }}"
                                                            wire:change="updateShare({{ $member->id }}, $event.target.value, '{{ $shareMode }}')"
                                                            class="min-w-16 p-1 text-xs border border-gray-300 rounded-l-md text-right"
                                                        />
                                                        <span class="bg-gray-100 p-1 text-xs border border-l-0 border-gray-300 rounded-r-md w-6 text-center">
                                                            {{ $shareMode === 'percentage' ? '%' : $this->getCurrencySymbol() }}
                                                        </span>
                                                        <button type="button" wire:click="removeShare({{ $member->id }})" class="ml-1 text-red-500 hover:text-red-700 w-5 h-5 flex-center">
                                                            <x-svg.cross />
                                                        </button>
                                                    </div>
                                                @else
                                                    <button type="button"
                                                            wire:click="updateShare({{ $member->id }}, {{ $shareMode === 'percentage' ? ($remainingPercentage > 0 ? $remainingPercentage : 0) : ($remainingAmount > 0 ? $remainingAmount : 0) }}, '{{ $shareMode }}')"
                                                            class="text-xs bg-indigo-100 hover:bg-indigo-200 px-1.5 py-1 rounded">
                                                        <x-svg.add2 class="text-indigo-700 w-3.5 h-3.5" />
                                                    </button>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @else
                            <div class="py-2 px-3 bg-yellow-50 border border-yellow-200 rounded-md text-xs text-yellow-600">
                                Veuillez saisir un montant pour pouvoir le répartir entre les membres.
                            </div>
                        @endif
                    </x-invoices.create.form-step>

                    {{-- Étape 3: Dates importantes --}}
                    <x-invoices.create.form-step
                        step="3" title="Étape 3 : Dates importantes"
                        description="Indiquez les dates importantes concernant cette facture."
                    >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-form.field-date label="Date d'émission" name="form.issued_date" model="form.issued_date" />
                            <x-form.field-date label="Date de paiement" name="form.payment_due_date" model="form.payment_due_date"/>
                            <x-form.field-date label="Rappel de paiement" name="form.payment_reminder" model="form.payment_reminder"/>
                            <x-form.select name="form.payment_frequency" model="form.payment_frequency" label="Fréquence de paiement">
                                <option value="" selected>Sélectionner une fréquence</option>
                                @foreach($paymentFrequencies as $value => $label)
                                    <option value="{{ $value }}">{!! $label !!}</option>
                                @endforeach
                            </x-form.select>
                        </div>
                    </x-invoices.create.form-step>

                    {{-- Étape 4: Statut de paiement --}}
                    <x-invoices.create.form-step
                        step="4" title="Étape 4 : Statut de paiement"
                        description="Indiquez le statut actuel de paiement de cette facture."
                        class="grid grid-cols-1 lg:grid-cols-2 gap-4"
                    >
                        <x-form.select name="form.payment_status" model="form.payment_status" label="Statut de la facture">
                            <option value="" selected>Sélectionner un statut</option>
                            @foreach($paymentStatuses as $value => $label)
                                <option value="{{ $value }}">{!! $label !!}</option>
                            @endforeach
                        </x-form.select>

                        <x-form.select name="form.payment_method" model="form.payment_method" label="Méthode de paiement utilisée">
                            <option value="" selected>Sélectionne un moyen de paiement</option>
                            @foreach($paymentMethods as $value => $label)
                                <option value="{{ $value }}">{!! $label !!}</option>
                            @endforeach
                        </x-form.select>

                        <x-form.select name="form.priority" model="form.priority" label="Étiquette de priorité">
                            <option value="" selected>Sélectionner une priorité</option>
                            @foreach($priorities as $value => $label)
                                <option value="{{ $value }}">{!! $label !!}</option>
                            @endforeach
                        </x-form.select>
                    </x-invoices.create.form-step>

                    {{-- Étape 5: Notes et tags personnalisés --}}
                    <x-invoices.create.form-step
                        step="5" title="Étape 5 : Notes et tags personnalisés"
                        description="Ajoutez des notes et des tags pour mieux organiser vos factures."
                    >
                        <x-form.field-textarea label="Notes (détail / commentaire important)" name="form.notes" model="form.notes" placeholder="Inscrivez votre message ici..."/>

                        <div class="text-right text-xs text-gray-500 mt-1 mr-2">
                            Montant de caractère maximum <span class="text-sm">{{ strlen($form->notes) }}</span>/500
                        </div>

                        <x-invoices.tag-manager
                            :tags="$form->tags"
                            :tag-input="$form->tagInput"
                            :show-suggestions="$showTagSuggestions"
                            :suggestions="$tagSuggestions"
                        />
                    </x-invoices.create.form-step>

                    <x-invoices.create.form-step
                        step="6" title="Étape 6 : Résumé"
                        description="Vérifiez les informations avant d'enregistrer la facture."
                    >
                        <!-- Résumé du formulaire -->
                        <x-invoices.create.summary :form="$form" :family_members="$family_members" />
                    </x-invoices.create.form-step>

                    {{-- Boutons de Navigation --}}
                    <div class="mt-6 border-t-[0.1rem] w-full border-dashed border-gray-200 dark:border-gray-700">
                        <div
                            class="mt-4 flex flex-wrap gap-4"
                            :class="{'justify-between': currentStep > 1, 'justify-end': currentStep === 1}"
                        >
                            <button type="button" x-show="currentStep > 1" @click="prevStep" class="button-secondary">
                                <x-svg.arrows.left class="stroke-white"/>
                                Précédent
                            </button>
                            <button
                                type="submit"
                                x-show="currentStep < steps.length"
                                class="max-sm:hidden relative button-classic group px-3 text-gray-600 hover:text-gray-700 bg-gray-200 hover:bg-gray-300"
                                aria-label="Terminer et valider toutes les étapes"
                            >
                                Terminer et valider
                                <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200 px-3 py-1.5 flex gap-2 bg-gray-700 text-white rounded-md whitespace-nowrap pointer-events-none">
                                     <x-svg.info class="mt-0.5 text-white w-3 h-3" />
                                    <span class="text-xs text-left max-w-xs">Cette action valide toutes les étapes et<br>enregistre la facture si il n'y a pas d'erreur.</span>
                              </span>
                            </button>
                            <button type="button" x-show="currentStep < steps.length" @click="nextStep" class="button-secondary">
                                Suivant
                                <x-svg.arrows.right class="stroke-white"/>
                            </button>
                            <button type="submit" x-show="currentStep === steps.length" class="button-tertiary" wire:loading.attr="disabled" @if($errors->any()) disabled @endif>
                                Valider
                                <x-svg.valid class="text-white"/>
                            </button>
                        </div>
                    </div>
                    {{-- Fin colonne 2 --}}
                </div>
            </div>

            {{-- Gestions des messages d'erreurs --}}
            <x-invoices.create.alert-errors :form="$form" />
        </form>
    </div>
</div>
