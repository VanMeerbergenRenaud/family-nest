@props([
    'form' => $form,
])

<div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
    <dl>
        <x-invoices.create.summary-item label="Nom de la facture" :alternateBackground="true">
            {{ ucfirst($form->name) ?: 'Non spécifié' }}
        </x-invoices.create.summary-item>

        <x-invoices.create.summary-item label="Type et catégorie">
            {{ $form->type ?: 'Non spécifié' }} - {{ $form->category ?: 'Non spécifié' }}
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

        <x-invoices.create.summary-item label="Répartition du montant" :alternateBackground="true">
            @if(!empty($form->amount))
                @if($form->paid_by)
                    <span class="block">
                        <span class="text-sm-regular">Payée par :</span>
                        <span class="text-sm-medium">{{ $form->paid_by }}</span>
                    </span>
                @endif
                @if($form->associated_members)
                    <span class="block">
                        <span class="text-sm-regular">Associée à :</span>
                        @foreach($form->associated_members as $member)
                            <span class="text-sm-medium">
                                {{ $member }}@if(!$loop->last), @endif
                            </span>
                        @endforeach
                    </span>
                @endif
            @else
                Non spécifié
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

        <x-invoices.create.summary-item label="Engagement" :alternateBackground="true">
            {{ $form->engagement_name ?: 'Non spécifié' }}
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
            <div class="flex flex-wrap gap-2">
                @if(empty($form->tags))
                   <span class="text-sm-medium">Aucun</span>
                @else
                    @foreach($form->tags as $tag)
                        <span class="px-2 py-1 rounded-full text-xs-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                            {{ $tag }}
                        </span>
                    @endforeach
                @endif
            </div>
        </x-invoices.create.summary-item>

        <x-invoices.create.summary-item label="Pièce jointe" :alternateBackground="true">
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
        </x-invoices.create.summary-item>
    </dl>
</div>
