@props([
    'form' => $form,
    'family_members' => collect(),
])

<div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
    <dl>
        <x-invoices.create.summary-item label="Nom de la facture" :alternateBackground="true">
            {{ ucfirst($form->name) ?: 'Non spécifié' }}
        </x-invoices.create.summary-item>

        <x-invoices.create.summary-item label="Type et catégorie">
            {{ ucfirst($form->type) ?: 'Non spécifié' }} - {{ ucfirst($form->category) ?: 'Non spécifié' }}
        </x-invoices.create.summary-item>

        <x-invoices.create.summary-item label="Fournisseur" :alternateBackground="true">
            {{ $form->issuer_name ?: 'Non spécifié' }}
            @if($form->issuer_website)
                <a href="{{ $form->issuer_website }}" target="_blank"
                   title="Visiter le site de l'émetteur"
                   class="text-indigo-500 text-sm-regular hover:text-indigo-700 ml-0.5"
                >
                    ({{ $form->issuer_website }})
                </a>
            @endif
        </x-invoices.create.summary-item>

        <x-invoices.create.summary-item label="Montant">
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
        </x-invoices.create.summary-item>

        <x-invoices.create.summary-item label="Montant et répartition" :alternateBackground="true">
            @if(!empty($form->amount))
                @php
                    // S'assurer que $family_members existe
                    $family_members = $family_members ?? collect();

                    // Récupérer les informations du payeur
                    $payerName = "Non spécifié";
                    $payerId = null;
                    $payerAvatar = null;

                    if ($form->paid_by_user_id) {
                        // Chercher l'utilisateur dans la liste des membres de famille
                        foreach ($family_members as $member) {
                            if ($member->id == $form->paid_by_user_id) {
                                $payerName = $member->name;
                                $payerId = $member->id;
                                $payerAvatar = $member->avatar_url;
                                break;
                            }
                        }
                    } else {
                        $payerName = $form->issuer_name;
                    }

                    // Calculer les totaux
                    $totalShared = 0;
                    $totalPercentage = 0;
                    if (!empty($form->user_shares)) {
                        foreach ($form->user_shares as $share) {
                            $totalShared += $share['amount'] ?? 0;
                            $totalPercentage += $share['percentage'] ?? 0;
                        }
                    }

                    // Déterminer le statut de la répartition
                    $isFullyShared = abs($totalPercentage - 100) < 0.1 || abs($totalShared - $form->amount) < 0.01;
                    $isOverShared = $totalPercentage > 100.1 || $totalShared > ($form->amount + 0.01);

                    // Calculer le montant restant
                    $remainingAmount = $form->amount - $totalShared;
                    $remainingPercentage = 100 - $totalPercentage;

                    // Formater pour l'affichage
                    $formattedTotal = number_format($form->amount, 2, ',', ' ');
                    $formattedShared = number_format($totalShared, 2, ',', ' ');
                    $formattedRemaining = number_format(abs($remainingAmount), 2, ',', ' ');
                @endphp

                <div class="space-y-2">
                    <!-- Nom du payeur -->
                    <div class="flex justify-between items-center">
                        <p class="max-sm:mt-1.5 text-sm-regular">
                            Payeur :
                            @if($payerName !== "Non spécifié")
                                <span class="text-sm-medium">
                            <img src="{{ $payerAvatar ?? asset('img/img_placeholder.jpg') }}" alt="" class="w-6 h-6 object-cover rounded-full inline-block ml-2 mr-1">
                            {{ $payerName }}
                        </span>
                            @else
                                <span class="text-sm-medium">Non spécifié</span>
                            @endif
                        </p>
                    </div>

                    <!-- Détail des parts -->
                    @if(!empty($form->user_shares))
                        <!-- Détail des parts avec toggle -->
                        <div class="mt-2 pr-4 max-sm:max-w-[70vw] overflow-x-scroll" x-data="{ showRepartition: false }">

                            <!-- En-tête avec bouton toggle -->
                            <button @click="showRepartition = !showRepartition"
                                    type="button"
                                    class="flex justify-between items-center w-full gap-3 pl-0 py-2 text-sm font-medium rounded-lg text-gray-700 transition-colors">

                                <div class="flex items-center gap-2">
                                    <div class="text-sm-regular">Répartition&nbsp;:</div>
                                    <div class="text-sm-medium w-max">{{ $formattedShared }}&nbsp;€</div>
                                    <div class="relative top-0.5 h-1 min-w-20 bg-gray-200 rounded-full">
                                        <div class="h-1 rounded-full bg-amber-500" style="width: {{ min($totalPercentage, 100) }}%"></div>
                                    </div>
                                    <div class="text-sm-medium">({{ number_format($totalPercentage, 0) }}%)</div>
                                </div>

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-4 w-4 transition-transform"
                                     :class="showRepartition ? 'transform rotate-180' : ''"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Détail des répartitions - collapsible -->
                            <ul x-show="showRepartition"
                                 x-transition
                                 x-collapse
                                 class="mt-1 pt-3 space-y-2 border-t border-slate-200">

                                @foreach($form->user_shares as $share)
                                    @php
                                        $memberName = "Membre inconnu";
                                        $memberAvatar = null;
                                        $memberObj = null;

                                        // Rechercher dans la liste des membres
                                        foreach ($family_members as $familyMember) {
                                            if ($familyMember->id == $share['id']) {
                                                $memberName = $familyMember->name;
                                                $memberAvatar = $familyMember->avatar_url;
                                                $memberObj = $familyMember;
                                                break;
                                            }
                                        }

                                        $sharePercentage = $share['percentage'] ?? 0;
                                        $shareAmount = $share['amount'] ?? 0;
                                        $isPayer = $share['id'] == $payerId;
                                    @endphp
                                    <li class="flex justify-between items-center py-1" wire:key="share-{{ $share['id'] }}">
                                        <div class="flex items-center gap-2 sm:w-44">
                                            <img src="{{ $memberAvatar ?? asset('img/img_placeholder.jpg') }}" alt="" class="w-6 h-6 rounded-full inline-block">
                                            <span class="text-sm text-gray-700">{{ $memberName }}</span>
                                        </div>
                                        <p class="text-sm-medium text-gray-800">{{ number_format($shareAmount, 2, ',', ' ') }} €</p>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-1 bg-gray-100 rounded-full">
                                                <div class="h-1 rounded-full {{ $isPayer ? 'bg-indigo-400' : 'bg-gray-400' }}"
                                                     style="width: {{ min($sharePercentage, 100) }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500 ml-1">{{ number_format($sharePercentage, 2) }}%</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <!-- Aucune répartition -->
                        <p class="text-sm-regular">
                            Répartition : <span class="text-sm-medium">Non définie</span>
                        </p>
                    @endif
                </div>
            @else
                <!-- Montant non défini -->
                <p class="text-sm-medium">
                    Non spécifié - Non définie
                </p>
            @endif
        </x-invoices.create.summary-item>

        <x-invoices.create.summary-item label="Dates">
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
        </x-invoices.create.summary-item>

        <x-invoices.create.summary-item label="Statut de paiement">
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
        </x-invoices.create.summary-item>

        <x-invoices.create.summary-item label="Notes" :alternateBackground="true">
            @if(!empty($form->notes))
                <span class="text-sm-regular">{{ $form->notes }}</span>
            @else
                <span class="text-sm-medium">{{ __('Aucune') }}</span>
            @endif
        </x-invoices.create.summary-item>

        <x-invoices.create.summary-item label="Tags">
            @if(empty($form->tags))
               <span class="text-sm-medium">Aucun</span>
            @else
                <ul class="flex flex-wrap gap-2">
                    @foreach($form->tags as $tag)
                        <li wire:key="tag-{{ $tag }}">
                            <span class="px-2 py-1 rounded-full text-xs-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                {{ $tag }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-invoices.create.summary-item>
    </dl>
</div>
