@props([
    'form' => $form,
    'family_members' => collect(),
])

<div class="w-full bg-white dark:bg-gray-800 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
    <dl>
        <x-invoices.create.summary-item label="Nom de la facture" :alternateBackground="true">
            {{ $form->name ?? 'Non spécifié' }}
        </x-invoices.create.summary-item>

        <x-invoices.create.summary-item label="Type et catégorie">
            {{ $form->type ?? 'Non spécifié' }} - {{ $form->category ?? 'Non spécifié' }}
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
                @php
                    try {
                        $currencyEnum = \App\Enums\CurrencyEnum::from($form->currency ?? 'EUR');
                        $formattedAmount = \Illuminate\Support\Number::format((float)$form->amount, 2, locale: 'fr_FR');
                        $symbol = $currencyEnum->symbol();
                        $flag = $currencyEnum->flag();
                        $currencyName = $currencyEnum->name();
                    } catch (\ValueError $e) {
                        $formattedAmount = \Illuminate\Support\Number::format((float)$form->amount, 2, locale: 'fr_FR');
                        $symbol = $form->currency ?? '€';
                        $flag = '';
                        $currencyName = $form->currency ?? 'EUR';
                    }
                @endphp

                <div class="flex items-center">
                    <span class="text-sm-medium">{{ $formattedAmount }}</span>
                    <span class="ml-1 text-sm-medium">{{ $symbol }}</span>
                    <span
                        class="ml-2 text-xs inline-flex items-center px-2 py-0.5 rounded-full bg-teal-100 text-gray-800">
                        {{ $flag }} {{ $currencyName }}
                    </span>
                </div>
            @else
                <span class="text-sm-medium">Non spécifié</span>
            @endif
        </x-invoices.create.summary-item>

        <x-invoices.create.summary-item label="Montant et répartition" :alternateBackground="true">
            @php
                $shareSummary = $this->getShareDetailSummary($family_members);
                $currencySymbol = '';
                try {
                    $currencyEnum = \App\Enums\CurrencyEnum::from($form->currency ?? 'EUR');
                    $currencySymbol = $currencyEnum->symbol();
                } catch (\ValueError $e) {
                    $currencySymbol = $form->currency ?? '€';
                }
            @endphp
            @if($shareSummary['hasAmount'])
                <div class="space-y-2">
                    <!-- Nom du payeur -->
                    <div class="flex justify-between items-center">
                        <p class="max-sm:mt-1.5 text-sm-regular">
                            Payeur :
                            @if($shareSummary['payer']['name'] !== "Non spécifié")
                                <span class="text-sm-medium">
                            <img src="{{ $shareSummary['payer']['avatar'] ?? asset('img/img_placeholder.jpg') }}" alt=""
                                 class="w-6 h-6 object-cover rounded-full inline-block ml-2 mr-1">
                            {{ $shareSummary['payer']['name'] }}
                        </span>
                            @else
                                <span class="text-sm-medium">Non spécifié</span>
                            @endif
                        </p>
                    </div>

                    <!-- Détail des parts -->
                    @if($shareSummary['hasDetails'])
                        <!-- Détail des parts avec toggle -->
                        <div class="mt-2 pr-4 max-sm:max-w-[70vw] overflow-x-scroll"
                             x-data="{ showRepartition: false }">

                            <!-- En-tête avec bouton toggle -->
                            <button @click="showRepartition = !showRepartition"
                                    type="button"
                                    class="flex justify-between items-center w-full gap-3 pl-0 py-2 text-sm font-medium rounded-lg text-gray-700 transition-colors">
                                <div class="flex items-center gap-2">
                                    <div class="text-sm-regular">Répartition&nbsp;:</div>
                                    <div class="text-sm-medium w-max">{{ $shareSummary['formattedShared'] }}
                                        &nbsp;{{ $currencySymbol }}</div>
                                    <div class="relative top-0.5 h-1 min-w-20 bg-gray-200 rounded-full">
                                        <div class="h-1 rounded-full bg-amber-500"
                                             style="width: {{ min($shareSummary['totalPercentage'], 100) }}%"></div>
                                    </div>
                                    <div class="text-sm-medium">
                                        ({{ number_format($shareSummary['totalPercentage'], 0) }}%)
                                    </div>
                                </div>

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-4 w-4 transition-transform"
                                     :class="showRepartition ? 'transform rotate-180' : ''"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Détail des répartitions - collapsible -->
                            <ul x-show="showRepartition"
                                x-transition
                                x-collapse
                                class="mt-1 pt-3 space-y-2 border-t border-slate-200">
                                @foreach($shareSummary['memberDetails'] as $member)
                                    <li class="flex justify-between items-center gap-2 py-1"
                                        wire:key="share-{{ $member['id'] }}">
                                        <div class="flex items-center gap-2 sm:w-44 mr-2">
                                            <img src="{{ $member['avatar'] ?? asset('img/img_placeholder.jpg') }}"
                                                 alt="" class="w-6 h-6 rounded-full inline-block">
                                            <span class="text-sm text-gray-700">{{ $member['name'] }}</span>
                                        </div>
                                        <p class="text-sm-medium text-gray-800">{{ $member['formattedAmount'] }}
                                            &nbsp;{{ $currencySymbol }}</p>
                                        <div class="flex items-center gap-2">
                                            <div class="w-10 h-1 bg-gray-100 rounded-full">
                                                <div
                                                    class="h-1 rounded-full {{ $member['isPayer'] ? 'bg-indigo-400' : 'bg-gray-400' }}"
                                                    style="width: {{ min($member['sharePercentage'], 100) }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500 ml-1">{{ $member['formattedPercentage'] }}%</span>
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
                        {{ $form->simpleDateFormat($form->issued_date) }}
                    </span>
                </p>
                <p class="text-sm-regular">
                    Paiement: <span class="text-sm-medium">
                        {{ $form->simpleDateFormat($form->payment_due_date) }}
                    </span>
                </p>
                <p class="text-sm-regular">
                    Rappel: <span class="text-sm-medium">
                        {{ $form->simpleDateFormat($form->payment_reminder) }}
                    </span>
                </p>
                <p class="text-sm-regular">
                    Fréquence: <span class="text-sm-medium">
                        @if($form->payment_frequency && $form->payment_frequency instanceof \App\Enums\PaymentFrequencyEnum)
                            {{ $form->payment_frequency->label() }}
                        @else
                            Non spécifiée
                        @endif
                    </span>
                </p>
            </div>
        </x-invoices.create.summary-item>

        <x-invoices.create.summary-item label="Statut de paiement">
            @if($form->payment_status && $form->payment_status instanceof \App\Enums\PaymentStatusEnum)
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
            @else
                <span
                    class="mb-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                    Non spécifié
                </span>
            @endif

            <div class="mt-1 ml-1 flex flex-col gap-1.5">
                <p class="text-sm-regular">
                    Méthode: <span class="text-sm-medium">
                        @if($form->payment_method && $form->payment_method instanceof \App\Enums\PaymentMethodEnum)
                            {{ $form->payment_method->label() }}
                        @else
                            Non spécifiée
                        @endif
                    </span>
                </p>
                <p class="text-sm-regular">
                    Priorité: <span class="text-sm-medium">
                        @if($form->priority && $form->priority instanceof \App\Enums\PriorityEnum)
                            {{ $form->priority->label() }}
                        @else
                            Non spécifiée
                        @endif
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
                            <span
                                class="px-2 py-1 rounded-full text-xs-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                {{ $tag }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-invoices.create.summary-item>
    </dl>
</div>
