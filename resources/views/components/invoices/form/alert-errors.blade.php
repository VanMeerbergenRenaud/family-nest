@props([
    'form',
])

<div class="my-6 lg:px-4 mx-auto max-lg:flex-center max-lg:flex-col gap-4 lg:grid lg:grid-cols-[1fr_2fr] lg:gap-x-10 lg:gap-y-0">
    @php
        // Utiliser une variable pour stocker la fonction
        $calculateProgressFn = function($form, $errors) {

            $requiredFields = ['uploadedFile', 'name', 'amount'];
            $optionalFields = [
                'type', 'category', 'issuer_name', 'issuer_website', 'issued_date',
                'payment_due_date', 'payment_reminder', 'payment_frequency', 'payment_status',
                'payment_method', 'priority', 'notes', 'tags'
            ];

            $filledRequired = 0;
            $filledOptional = 0;

            // Vérifier si fichier est valide (présent ET sans erreur)
            if ((!empty($form->uploadedFile) || !empty($form->existingFilePath)) &&
                !$errors->has('form.uploadedFile')) {
                $filledRequired++;
            }

            // Vérifier le nom est présent ET sans erreur
            if (!empty($form->name) && !$errors->has('form.name')) {
                $filledRequired++;
            }

            // Vérifier le montant est présent ET sans erreur
            if (isset($form->amount) && $form->amount !== '' && !$errors->has('form.amount')) {
                $filledRequired++;
            }

            // Vérifier les champs optionnels
            if (!empty($form->type)) $filledOptional++;
            if (!empty($form->category)) $filledOptional++;
            if (!empty($form->issuer_name)) $filledOptional++;
            if (!empty($form->issuer_website)) $filledOptional++;
            if (!empty($form->issued_date)) $filledOptional++;
            if (!empty($form->payment_due_date)) $filledOptional++;
            if (!empty($form->payment_reminder)) $filledOptional++;
            if (!empty($form->payment_frequency)) $filledOptional++;
            if (!empty($form->payment_status)) $filledOptional++;
            if (!empty($form->payment_method)) $filledOptional++;
            if (!empty($form->priority)) $filledOptional++;
            if (!empty($form->notes)) $filledOptional++;
            if (!empty($form->tags) && is_array($form->tags) && count($form->tags) > 0) $filledOptional++;

            // Calcul du pourcentage (70% pour les champs obligatoires + 30% pour les champs optionnels)
            $requiredPercentage = ($filledRequired / count($requiredFields)) * 70;
            $optionalPercentage = ($filledOptional / count($optionalFields)) * 30;
            $totalPercentage = round($requiredPercentage + $optionalPercentage);

            // Garantir que le pourcentage est entre 0 et 100
            $totalPercentage = max(0, min(100, $totalPercentage));

            return [
                'percentage' => $totalPercentage,
                'requiredCount' => $filledRequired,
                'totalRequired' => count($requiredFields)
            ];
        };

        // Calculer une seule fois la progression en appelant la fonction anonyme
        $progressData = $calculateProgressFn($form, $errors);
        $progressPercentage = $progressData['percentage'];

        // Déterminer le type d'alerte et la couleur en fonction du pourcentage
        if ($progressPercentage == 0) {
            $alertType = 'warning';
            $barColor = 'bg-gray-400';
            $alertTitle = 'Aucune information';
        } elseif ($progressPercentage < 33) {
            $alertType = 'warning';
            $barColor = 'bg-yellow-400';
            $alertTitle = 'Début de saisie';
        } elseif ($progressPercentage < 70) {
            $alertType = $errors->any() ? 'warning' : 'inProgress';
            $barColor = $errors->any() ? 'bg-yellow-400' : 'bg-orange-400';
            $alertTitle = 'En cours de complétion';
        } elseif ($progressPercentage < 100) {
            $alertType = $errors->any() ? 'warning' : 'almostComplete';
            $barColor = $errors->any() ? 'bg-yellow-400' : 'bg-emerald-400';
            $alertTitle = 'Presque terminé';
        } else {
            $alertType = $errors->any() ? 'warning' : 'success';
            $barColor = $errors->any() ? 'bg-yellow-400' : 'bg-green-500';
            $alertTitle = $errors->any() ? 'Corrections requises' : 'Prêt à soumettre';
        }

        // Vérifier s'il y a des erreurs spécifiques au fichier
        $hasFileError = $errors->has('form.uploadedFile');
    @endphp

        <!-- Affichage des erreurs de validation -->
    @if($errors->any())
        <x-form.alert type="{{ $alertType }}" title="Attention : {{ $alertTitle }}">
            <p class="text-sm-regular">
                Veuillez corriger toutes les erreurs avant de soumettre le formulaire.
                Vous pouvez naviguer vers les étapes précédentes pour effectuer les corrections nécessaires.
            </p>

            <div class="mt-4">
                <p class="flex justify-between mb-1">
                    <span class="text-xs-medium">Progression du formulaire</span>
                    <span class="text-xs-medium">{{ $progressPercentage }}%</span>
                </p>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="{{ $barColor }} h-2.5 rounded-full" style="width: {{ $progressPercentage }}%"></div>
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
                        'uploadedFile' => 0,
                        'fichier' => 0,
                        'taille' => 0,
                        'format' => 0,
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
                        'date d\'échéance' => 3,
                        'date de rappel' => 3,
                        'fréquence' => 3,
                        'statut' => 4,
                        'méthode de paiement' => 4,
                        'priorité' => 4,
                        'notes' => 5,
                        'tags' => 5,
                    ];

                    $errorsByStep = [];
                    $fileErrors = [];

                    // Grouper les erreurs par étape
                    foreach ($errors->all() as $error) {
                        $step = 1;
                        $isFileError = false;

                        // Si ce n'est pas une erreur de fichier basée sur les mots-clés, utiliser la méthode de correspondance des champs
                        if (!$isFileError) {
                            foreach ($fieldToStep as $field => $fieldStep) {
                                if (stripos($error, $field) !== false) {
                                    $step = $fieldStep;
                                    // Marquer que c'est une erreur liée au fichier
                                    if ($fieldStep === 0) {
                                        $isFileError = true;
                                    }
                                    break;
                                }
                            }
                        }

                        if ($isFileError) {
                            $fileErrors[] = $error;
                        } else {
                            if (!isset($errorsByStep[$step])) {
                                $errorsByStep[$step] = [];
                            }
                            $errorsByStep[$step][] = $error;
                        }
                    }

                    // Trier par numéro d'étape
                    ksort($errorsByStep);
                @endphp

                {{-- Afficher d'abord les erreurs liées au fichier (étape 0) --}}
                @if(count($fileErrors) > 0)
                    <div class="flex items-center justify-between border-b border-red-100">
                        <p class="pl-2 text-sm-bold text-red-600">Étape indispensable</p>
                        <label for="direct-file-upload" class="button-classic gap-2 text-red-600 pr-3.5 py-1.5 hover:bg-red-100 cursor-pointer">
                            Importer ici
                            <x-svg.download class="text-red-600" />
                            <input
                                id="direct-file-upload"
                                name="uploadedFile"
                                type="file"
                                wire:model="form.uploadedFile"
                                accept=".pdf,.docx,.jpeg,.jpg,.png"
                                class="hidden"
                            />
                        </label>
                    </div>
                    <ul class="pl-5 space-y-2 list-disc">
                        @foreach($fileErrors as $error)
                            <li class="text-sm-regular text-red-600">{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                {{-- Afficher les autres erreurs par étape --}}
                @foreach ($errorsByStep as $step => $stepErrors)
                    <div class="flex items-center justify-between border-b border-red-100">
                        <p class="pl-2 text-sm-bold text-red-600">Étape {{ $step }}</p>
                        <button type="button" @click="goToStep({{ $step }})" class="button-classic text-red-600 pr-3 py-1.5 hover:bg-red-100">
                            Aller à cette étape
                            <x-svg.arrows.right class="text-red-600"/>
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
        {{-- Pas d'image importée --}}
    @elseif(empty($form->uploadedFile) && empty($form->existingFilePath))
        <x-form.alert type="{{ $alertType }}" title="Aucune facture importée">
            <p class="text-sm-regular">
                Veuillez importer une facture pour commencer.
            </p>

            <div class="mt-4">
                <div class="flex justify-between mb-1">
                    <span class="text-xs-medium">Progression du formulaire</span>
                    <span class="text-xs-medium">{{ $progressPercentage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="{{ $barColor }} h-2.5 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                </div>
                <p class="text-xs mt-2">Commencez par importer votre facture pour continuer votre progression</p>
            </div>
        </x-form.alert>
        {{-- Image importée mais champs obligatoires vides ou partiellement remplis --}}
    @else
        <x-form.alert type="{{ $alertType }}" title="{{ $alertTitle }}">
            <p class="text-sm-regular">
                @if($progressPercentage == 100)
                    Toutes les informations requises sont complètes. Vous pouvez maintenant enregistrer cette facture si vous le souhaitez.
                @elseif($progressPercentage >= 70)
                    Vous avez rempli tous les champs obligatoires ! Complétez les champs restants pour une meilleure expérience.
                @else
                    Veuillez remplir tous les champs obligatoires suivis d'un astérisque *.
                @endif
            </p>

            <div class="mt-4">
                <div class="flex justify-between mb-1">
                    <span class="text-xs-medium">Progression du formulaire</span>
                    <span class="text-xs-medium">{{ $progressPercentage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="{{ $barColor }} h-2.5 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                </div>
                <p class="text-xs mt-2">
                    @if($progressPercentage == 100)
                        Félicitations ! Vous avez complété tous les champs nécessaires.
                    @elseif($progressPercentage >= 70)
                        Encore quelques champs et vous aurez terminé !
                    @elseif($progressPercentage > 0)
                        Continuez en remplissant les champs obligatoires suivis d'un astérisque *.
                    @endif
                </p>
            </div>
        </x-form.alert>
    @endif
</div>
