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
