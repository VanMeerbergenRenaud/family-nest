@props(['form'])

<div class="my-6 lg:px-4 mx-auto max-lg:flex-center max-lg:flex-col gap-4 lg:grid lg:grid-cols-[1fr_2fr] lg:gap-x-10 lg:gap-y-0">
    @php
        // Définition des champs obligatoires et optionnels
        $requiredFields = ['uploadedFile' => 'form.uploadedFile', 'name' => 'form.name', 'amount' => 'form.amount'];
        $optionalFields = ['type', 'category', 'issuer_name', 'issuer_website', 'issued_date', 'payment_due_date', 'payment_reminder', 'payment_frequency', 'payment_status', 'payment_method', 'priority', 'notes', 'tags'];

        // Vérifier les champs obligatoires
        $filledRequired = 0;

        if ((!empty($form->uploadedFile) || !empty($form->existingFilePath)) && !$errors->has('form.uploadedFile')) {
            $filledRequired++;
        }

        if (!empty($form->name) && !$errors->has('form.name')) {
            $filledRequired++;
        }

        if (isset($form->amount) && $form->amount !== '' && !$errors->has('form.amount')) {
            $filledRequired++;
        }

        // Compter les champs optionnels remplis
        $filledOptional = 0;

        foreach ($optionalFields as $field) {
            if ($field === 'tags') {
                if (!empty($form->tags) && is_array($form->tags) && count($form->tags) > 0) {
                    $filledOptional++;
                }
            } elseif (!empty($form->{$field})) {
                $filledOptional++;
            }
        }


        // Calcul du pourcentage de progression
        $requiredWeight = 70;
        $optionalWeight = 30;
        $requiredPercentage = ($filledRequired / count($requiredFields)) * $requiredWeight;
        $optionalPercentage = ($filledOptional / count($optionalFields)) * $optionalWeight;
        $progressPercentage = max(0, min(100, round($requiredPercentage + $optionalPercentage)));

        // Configuration de l'alerte selon le pourcentage et les erreurs
        $hasErrors = $errors->any();
        $hasFile = !empty($form->uploadedFile) || !empty($form->existingFilePath);

        // Déterminer le type d'alerte, la couleur et le titre
        $alertConfig = match(true) {
            $progressPercentage === 0 => [
                'type' => 'warning',
                'color' => 'bg-gray-400',
                'title' => 'Aucune information'
            ],
            $progressPercentage < 33 => [
                'type' => 'warning',
                'color' => 'bg-yellow-400',
                'title' => 'Début de saisie'
            ],
            $progressPercentage < 70 => [
                'type' => $hasErrors ? 'warning' : 'inProgress',
                'color' => $hasErrors ? 'bg-yellow-400' : 'bg-orange-400',
                'title' => 'En cours de complétion'
            ],
            $progressPercentage < 100 => [
                'type' => $hasErrors ? 'warning' : 'almostComplete',
                'color' => $hasErrors ? 'bg-yellow-400' : 'bg-emerald-400',
                'title' => 'Presque terminé'
            ],
            default => [
                'type' => $hasErrors ? 'warning' : 'success',
                'color' => $hasErrors ? 'bg-yellow-400' : 'bg-green-500',
                'title' => $hasErrors ? 'Corrections requises' : 'Prêt à soumettre'
            ]
        };

        // Définir le message principal
        $mainMessage = match(true) {
            $hasErrors => 'Veuillez corriger toutes les erreurs avant de soumettre le formulaire.',
            !$hasFile => 'Veuillez importer une facture pour commencer.',
            $progressPercentage === 100 => 'Toutes les informations requises sont complètes. Vous pouvez maintenant enregistrer cette facture.',
            $progressPercentage >= 70 => 'Vous avez rempli tous les champs obligatoires ! Complétez les champs restants pour une meilleure expérience.',
            default => 'Veuillez remplir tous les champs obligatoires suivis d\'un astérisque *.'
        };

        // Définir le message sous la barre de progression
        $progressMessage = match(true) {
            $hasErrors => 'Veuillez corriger les erreurs ci-contre avant de tout valider.',
            !$hasFile => 'Commencez par importer votre facture pour continuer votre progression.',
            $progressPercentage === 100 => 'Félicitations ! Vous avez complété tous les champs nécessaires.',
            $progressPercentage >= 70 => 'Encore quelques champs et vous aurez terminé !',
            default => 'Continuez en remplissant les champs obligatoires suivis d\'un astérisque *.'
        };

        // Regrouper les erreurs par étape
        $errorsByStep = [];
        $fileErrors = [];

        if ($hasErrors) {
            $fieldToStep = [
                'uploadedFile' => 0, 'fichier' => 0, 'taille' => 0, 'format' => 0,
                'nom' => 1, 'référence' => 1, 'type' => 1, 'catégorie' => 1, 'fournisseur' => 1, 'site internet' => 1,
                'montant' => 2, 'devise' => 2, 'payée par' => 2,
                'date d\'émission' => 3, 'date d\'échéance' => 3, 'date de rappel' => 3, 'fréquence' => 3,
                'statut' => 4, 'méthode de paiement' => 4, 'priorité' => 4,
                'notes' => 5, 'tags' => 5,
            ];

            foreach ($errors->all() as $error) {
                $isFileError = false;
                $step = 1;

                foreach ($fieldToStep as $field => $fieldStep) {
                    if (stripos($error, $field) !== false) {
                        $step = $fieldStep;
                        $isFileError = ($fieldStep === 0);
                        break;
                    }
                }

                if ($isFileError) {
                    $fileErrors[] = $error;
                } else {
                    $errorsByStep[$step][] = $error;
                }
            }

            ksort($errorsByStep);
        }
    @endphp

    {{-- Alerte de progression --}}
    <x-form.alert type="{{ $alertConfig['type'] }}" title="{{ $alertConfig['title'] }}">
        <p class="text-sm-regular">{{ $mainMessage }}</p>

        <div class="mt-4">
            <div class="flex justify-between mb-1">
                <span class="text-xs-medium">Progression du formulaire</span>
                <span class="text-xs-medium">{{ $progressPercentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="{{ $alertConfig['color'] }} h-2.5 rounded-full" style="width: {{ $progressPercentage }}%"></div>
            </div>
            <p class="text-xs mt-2">{{ $progressMessage }}</p>
        </div>
    </x-form.alert>

    {{-- Liste des erreurs (uniquement si des erreurs existent) --}}
    @if($hasErrors)
        <x-form.alert type="error" title="Veuillez corriger les erreurs suivantes avant de continuer :" layout="header">
            <div class="flex flex-col gap-2">

                {{-- Erreurs du fichier à uploader --}}
                @if(count($fileErrors) > 0)
                    <div class="flex items-center justify-between border-b border-red-100">
                        <p class="pl-2 text-sm-bold text-red-600">
                            Étape indispensable
                        </p>
                        <label for="form.uploadedFile" class="button-classic gap-2 text-red-600 pr-3.5 py-1.5 hover:bg-red-100 cursor-pointer">
                            Importer ici
                            <x-svg.download class="text-red-600"/>
                        </label>
                        <input
                            id="form.uploadedFile"
                            name="form.uploadedFile"
                            type="file"
                            wire:model="form.uploadedFile"
                            accept=".pdf,.docx,.jpeg,.jpg,.png"
                            class="hidden"
                        />
                    </div>
                    <ul class="pl-5 space-y-2 list-disc">
                        @foreach($fileErrors as $error)
                            <li class="text-sm-regular text-red-600">{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                {{-- Autres erreurs par étape --}}
                @foreach($errorsByStep as $step => $errors)
                    <div class="flex items-center justify-between border-b border-red-100">
                        <p class="pl-2 text-sm-bold text-red-600">Étape {{ $step }}</p>
                        <button type="button" @click="goToStep({{ $step }})"
                                class="button-classic text-red-600 pr-3 py-1.5 hover:bg-red-100"
                        >
                            Aller à cette étape
                            <x-svg.arrows.right class="text-red-600"/>
                        </button>
                    </div>

                    <ul class="pl-5 space-y-2 list-disc">
                        @foreach($errors as $error)
                            <li class="text-sm-regular text-red-600">{{ $error }}</li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        </x-form.alert>
    @endif
</div>
