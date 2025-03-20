<div>

    @if($isEditMode)
        <div class="mx-auto max-w-[65rem] p-3 mb-8 rounded-md bg-blue-100 border-l-4 border-blue-500 text-blue-700">
            <div class="flex-center">
                <x-svg.edit class="w-5 h-5 mr-2 text-blue-700" />
                <span class="font-medium">Mode édition de la facture</span>
            </div>
        </div>
    @endif

    <h1 class="sr-only">Modifier la facture</h1>

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
                                    class="absolute top-2.5 right-2.5 z-2"
                            >
                                <x-svg.cross class="text-red-600 hover:text-black bg-red-300 hover:bg-red-400 rounded-full w-6 h-6 p-1 transition-colors duration-200" />
                            </button>

                            @php
                                $fileName = $form->fileName;
                                $fileExtension = $form->fileExtension;
                                $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png']);
                                $isPdf = $fileExtension === 'pdf';
                                $isDocx = $fileExtension === 'docx';
                                $isCsv = $fileExtension === 'csv';
                                $fileSize = $form->fileSize;
                                $sizeFormatted = $form->formatFileSize($fileSize);
                                $storagePath = Storage::url($form->existingFilePath);
                            @endphp

                            <div class="rounded-xl border border-slate-200 min-h-[30rem] flex flex-col items-center justify-center p-2 overflow-y-scroll">
                                <!-- Aperçu pour les images -->
                                @if ($isImage)
                                    <img src="{{ $storagePath }}"
                                         alt="Aperçu de la facture"
                                         class="bg-gray-100 rounded-xl max-h-[50vh]"
                                    />
                                @elseif ($isPdf)
                                    <div class="w-full h-[40vh] overflow-hidden rounded-xl">
                                        <embed src="{{ $storagePath }}"
                                               type="application/pdf"
                                               width="100%"
                                               height="100%"
                                               class="rounded-xl"
                                        />
                                    </div>
                                @elseif ($isDocx)
                                    <div class="px-4 mb-4 text-center">
                                        <div class="p-5 text-gray-700 text-md-medium border border-slate-200 rounded-xl bg-slate-100">
                                            <p class="mb-2.5 font-medium text-slate-700">Aperçu non disponible pour les fichiers Word</p>
                                            <p class="text-sm text-slate-500">Le fichier a été traité et sauvegardé</p>
                                        </div>
                                    </div>
                                @elseif($isCsv)
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
                                    <h2 class="text-md-medium text-gray-800 truncate">{{ $fileName }}</h2>
                                    <p class="flex-center space-x-1.5 text-gray-600">
                                        <span class="text-sm-regular">{{ strtoupper($fileExtension) }}</span>
                                        <span class="text-sm-regular">{{ $sizeFormatted }}</span>
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
                                    class="absolute top-2.5 right-2.5 z-2"
                            >
                                <x-svg.cross class="text-red-600 hover:text-black bg-red-300 hover:bg-red-400 rounded-full w-6 h-6 p-1 transition-colors duration-200" />
                            </button>

                            @php
                                $fileInfo = $form->getFileInfo();
                                $fileName = $fileInfo['name'] ?? '';
                                $fileExtension = $fileInfo['extension'] ?? '';
                                $isImage = $fileInfo['isImage'] ?? false;
                                $isPdf = $fileInfo['isPdf'] ?? false;
                                $isDocx = $fileInfo['isDocx'] ?? false;
                                $isCsv = $fileInfo['isCsv'] ?? false;
                                $fileSize = $fileInfo['size'] ?? 0;
                                $sizeFormatted = $fileInfo['sizeFormatted'] ?? '';
                            @endphp

                            <div class="rounded-xl border border-slate-200 min-h-[30rem] flex flex-col items-center justify-center p-2 overflow-y-scroll">
                                <!-- Aperçu pour les images -->
                                @if ($isImage)
                                    <img src="{{ $form->uploadedFile->temporaryUrl() }}"
                                         alt="Aperçu de la facture"
                                         class="bg-gray-100 rounded-xl max-h-[50vh]"
                                    />
                                @elseif ($isPdf)
                                    <div class="w-full h-[40vh] overflow-hidden rounded-xl">
                                        <embed src="{{ $form->uploadedFile->temporaryUrl() }}"
                                               type="application/pdf"
                                               width="100%"
                                               height="100%"
                                               class="rounded-xl"
                                        />
                                    </div>
                                @elseif ($isDocx)
                                    <div class="px-4 mb-4 text-center">
                                        <div class="p-5 text-gray-700 text-md-medium border border-slate-200 rounded-xl bg-slate-100">
                                            <p class="mb-2.5 font-medium text-slate-700">Aperçu non disponible pour les fichiers Word</p>
                                            <p class="text-sm text-slate-500">Le fichier sera traité après l'enregistrement de la facture</p>
                                        </div>
                                    </div>
                                @elseif($isCsv)
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
                                    <h2 class="text-md-medium text-gray-800 truncate">{{ $fileName }}</h2>
                                    <p class="flex-center space-x-1.5 text-gray-600">
                                        <span class="text-sm-regular">{{ strtoupper($fileExtension) }}</span>
                                        <span class="text-sm-regular">{{ $sizeFormatted }}</span>
                                    </p>

                                    @if(!$errors->has('form.uploadedFile'))
                                        <p class="mt-2 px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 w-fit">
                                            Nouveau fichier à importer
                                        </p>
                                    @else
                                        <p class="mt-2 px-3 py-1 text-xs rounded-full bg-red-100 text-red-800 w-fit">
                                            Erreur lors de l'import du fichier
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Champ d'upload vide --}}
                        <x-form.field-upload label="Importer une nouvelle facture" model="form.uploadedFile" name="form.uploadedFile" :asterix="false" />
                    @endif
                </div>

                {{-- Steps : colonne 2 --}}
                <div class="bg-slate-100 lg:max-w-[60vw] flex flex-col justify-between py-4 px-6 rounded-xl border border-slate-200">

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
                                    <option value="{{ $typeValue }}">{{ $typeLabel }}</option>
                                @endforeach
                            </x-form.select>

                            <x-form.select label="Catégorie*" name="form.category" model="form.category" label="Catégorie">
                                <option value="" selected>Sélectionner une catégorie</option>
                                @foreach($form->availableCategories as $availableCategory)
                                    <option value="{{ $availableCategory }}">{{ $availableCategory }}</option>
                                @endforeach
                            </x-form.select>

                            <x-form.field label="Fournisseur / émetteur de la facture" name="form.issuer_name" model="form.issuer_name" placeholder="Nom du fournisseur"/>
                            <x-form.field label="Site internet du fournisseur" name="form.issuer_website" model="form.issuer_website" placeholder="https://monfournisseur.com"/>
                        </div>

                    </x-invoices.create.form-step>

                    {{-- Étape 2: Détails financiers --}}
                    <x-invoices.create.form-step
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

                        <x-form.select name="form.associated_members" model="form.associated_members" label="Associé à un membre de la famille">
                            <option value="" disabled>Sélectionner un membre</option>
                            @foreach($family_members as $member)
                                <option value="{{ $member->name }}">{{ $member->name }}</option>
                            @endforeach
                        </x-form.select>
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
                                <option value="monthly">Mensuel</option>
                                <option value="quarterly">Trimestriel</option>
                                <option value="annually">Annuel</option>
                                <option value="one_time">Ponctuel</option>
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
                            <option value="unpaid">Non payée</option>
                            <option value="paid">Payée</option>
                            <option value="late">En retard</option>
                            <option value="partially_paid">Partiellement payée</option>
                        </x-form.select>


                        <x-form.select name="form.payment_method" model="form.payment_method" label="Méthode de paiement utilisée">
                            <option value="" selected>Sélectionne un moyen de paiement</option>
                            <option value="card">Carte bancaire</option>
                            <option value="cash">Espèces</option>
                            <option value="transfer">Virement</option>
                        </x-form.select>

                        <x-form.select name="form.priority" model="form.priority" label="Étiquette de priorité">
                            <option value="" selected>Sélectionner une priorité</option>
                            <option value="low">Basse</option>
                            <option value="medium">Moyenne</option>
                            <option value="high">Élevée</option>
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
                    </x-invoices.create.form-step>

                    <x-invoices.create.form-step
                        step="6" title="Étape 6 : Résumé"
                        description="Vérifiez les informations avant d'enregistrer la facture."
                    >

                        <!-- Résumé du formulaire -->
                        <x-invoices.create.summary :form="$form" />

                    </x-invoices.create.form-step>

                    {{-- Boutons de Navigation --}}
                    <div class="mt-6 border-t-[0.1rem] w-full border-dashed border-gray-200 dark:border-gray-700">
                        <div class="mt-4 flex flex-wrap justify-between gap-4">
                            <button type="button" x-show="currentStep > 1" @click="prevStep" class="button-secondary">
                                <x-svg.arrows.left class="stroke-white"/>
                                Précédent
                            </button>
                            <button type="button" x-show="currentStep < steps.length" @click="nextStep" class="ml-auto button-secondary">
                                Suivant
                                <x-svg.arrows.right class="stroke-white"/>
                            </button>
                            <button type="submit" x-show="currentStep < steps.length" class="button-primary">
                                Tout sauvegarder
                            </button>
                            <button type="submit" x-show="currentStep === steps.length" class="button-tertiary">
                                Sauvegarder
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
