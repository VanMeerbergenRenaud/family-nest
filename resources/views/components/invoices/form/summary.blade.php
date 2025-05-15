@props([
    'form' => $form,
    'family_members' => collect(),
])

<div class="w-full bg-white dark:bg-gray-800 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
    <dl>
        <x-invoices.form.summary-item label="Nom et référence" :alternateBackground="true">
            <div class="flex flex-wrap items-center jus gap-1">
                <span class="text-sm-medium">{{ $form->name ?? __('Non spécifié') }}</span>&nbsp;-&nbsp;
                <span class="text-sm-medium"> {{ $form->reference ?? __('Non spécifié') }}</span>
            </div>
        </x-invoices.form.summary-item>

        <x-invoices.form.summary-item label="Type et catégorie">
            {{ $form->type ?? __('Non spécifié') }} - {{ $form->category ?? __('Non spécifié') }}
        </x-invoices.form.summary-item>

        <x-invoices.form.summary-item label="Fournisseur" :alternateBackground="true">
            {{ $form->issuer_name ?? __('Non spécifié') }}
            @if($form->issuer_website)
                <a href="{{ $form->issuer_website }}" target="_blank"
                   title="Visiter le site de l'émetteur"
                   class="text-indigo-500 text-sm-regular hover:text-indigo-700 ml-0.5"
                >
                    ({{ $form->issuer_website }})
                </a>
            @endif
        </x-invoices.form.summary-item>

        <x-invoices.form.summary-item label="Montant">
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
                <span class="text-sm-medium">{{ __('Non spécifié') }}</span>
            @endif
        </x-invoices.form.summary-item>

        <x-invoices.form.summary-item label="Montant et répartition" :alternateBackground="true">
            @if(isset($invoice) && $invoice->amount > 0)
                <div class="space-y-2">
                    <!-- Nom du payeur - toujours l'afficher si un payeur est défini -->
                    <div class="flex justify-between items-center">
                        @if($invoice->paidByUser)
                            <p class="max-sm:mt-1.5 text-sm-regular">
                                Payeur :
                                <span class="text-sm-medium">
                            <img src="{{ $invoice->paidByUser->avatar_url ?? asset('img/img_placeholder.jpg') }}"
                                 class="w-6 h-6 object-cover rounded-full inline-block ml-2 mr-1"
                                 alt="" loading="lazy"
                            >
                            {{ $invoice->paidByUser->name }}
                        </span>
                            </p>
                        @endif
                    </div>

                    <!-- Détail des parts - seulement s'il y a des partages -->
                    @if($invoice->has_shares)
                        <!-- Détail des parts avec toggle -->
                        <div x-data="{ showRepartition: false }"
                             class="mt-2 pr-4 max-sm:max-w-[70vw] overflow-x-scroll"
                        >
                            <button
                                type="button"
                                @click="showRepartition = !showRepartition"
                                class="flex justify-between items-center w-full gap-3 pl-0 py-2 text-sm-medium rounded-lg text-gray-700 transition-colors"
                            >
                                <div class="flex items-center gap-2">
                                    <div class="text-sm-regular">Répartition&nbsp;:</div>
                                    <div class="text-sm-medium w-max">{{ number_format($invoice->amount, 2, ',', ' ') }}
                                        &nbsp;{{ $currencySymbol }}</div>
                                    <div class="relative top-0.5 h-1 min-w-20 bg-gray-200 rounded-full">
                                        <div class="h-1 rounded-full bg-amber-500"
                                             style="width: {{ min($invoice->total_percentage, 100) }}%"></div>
                                    </div>
                                    <div class="text-sm-medium">
                                        ({{ number_format($invoice->total_percentage, 0) }}%)
                                    </div>
                                </div>

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-4 w-4 transition-transform"
                                     :class="showRepartition ? 'transform rotate-180' : ''"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Détail des répartitions - collapsible -->
                            <ul x-show="showRepartition"
                                x-transition
                                x-collapse
                                class="mt-1 pt-3 space-y-2 border-t border-slate-200">
                                @foreach($invoice->sharings as $sharing)
                                    <li class="flex justify-between items-center gap-2 py-1"
                                        wire:key="share-{{ $sharing->user_id }}">
                                        <div class="flex items-center gap-2 sm:w-44 mr-2">
                                            <img
                                                src="{{ $sharing->user->avatar_url ?? asset('img/img_placeholder.jpg') }}"
                                                class="w-6 h-6 rounded-full inline-block"
                                                alt="" loading="lazy"
                                            >
                                            <span class="text-sm text-gray-700">{{ $sharing->user->name }}</span>
                                        </div>
                                        <p class="text-sm-medium text-gray-800">
                                            {{ $sharing->formatted_amount }}&nbsp;{{ $currencySymbol }}
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <div class="w-12 h-1 bg-gray-100 rounded-full">
                                                <div
                                                    class="h-1 rounded-full {{ $sharing->isPayer() ? 'bg-indigo-400' : 'bg-gray-400' }}"
                                                    style="width: {{ min($sharing->share_percentage, 100) }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500 ml-1">{{ $sharing->formatted_percentage }}%</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-sm-regular">
                            Répartition : <span class="text-sm-medium">{{ __('Non définie') }}</span>
                        </p>
                    @endif
                </div>
            @elseif(isset($form) && $form->amount > 0)
                <div class="space-y-2">
                    <!-- Nom du payeur -->
                    <div class="flex justify-between items-center">
                        @php
                            $payer = isset($family_members) ? $family_members->firstWhere('id', $form->paid_by_user_id) : null;
                        @endphp
                        @if($payer)
                            <p class="max-sm:mt-1.5 text-sm-regular">
                                Payeur :
                                <span class="text-sm-medium">
                        <img src="{{ $payer->avatar_url ?? asset('img/img_placeholder.jpg') }}"
                             class="w-6 h-6 object-cover rounded-full inline-block ml-2 mr-1"
                             alt="" loading="lazy"
                        >
                        {{ $payer->name }}
                    </span>
                            </p>
                        @endif
                    </div>

                    <!-- Détail des parts -->
                    @php
                        $shareSummary = $this->getShareSummary();
                        $hasShares = !empty($form->user_shares) && $shareSummary['totalShares'] > 0;
                    @endphp

                    @if($hasShares)
                        <!-- Détail des parts avec toggle -->
                        <div x-data="{ showRepartition: false }"
                             class="mt-2 pr-4 max-sm:max-w-[70vw] overflow-x-scroll"
                        >
                            <button
                                type="button"
                                @click="showRepartition = !showRepartition"
                                class="flex justify-between items-center w-full gap-3 pl-0 py-2 text-sm-medium rounded-lg text-gray-700 transition-colors"
                            >
                                <div class="flex items-center gap-2">
                                    <div class="text-sm-regular">Répartition&nbsp;:</div>
                                    <div class="text-sm-medium w-max">{{ number_format($form->amount, 2, ',', ' ') }}
                                        &nbsp;{{ $this->getCurrencySymbol() }}</div>
                                    <div class="relative top-0.5 h-1 min-w-20 bg-gray-200 rounded-full">
                                        <div class="h-1 rounded-full bg-amber-500"
                                             style="width: {{ min($shareSummary['totalPercent'], 100) }}%"></div>
                                    </div>
                                    <div class="text-sm-medium">
                                        ({{ $shareSummary['formattedTotalPercent'] }})
                                    </div>
                                </div>

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-4 w-4 transition-transform"
                                     :class="showRepartition ? 'transform rotate-180' : ''"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Détail des répartitions - collapsible -->
                            <ul x-show="showRepartition"
                                x-transition
                                x-collapse
                                class="mt-1 pt-3 space-y-2 border-t border-slate-200">
                                @foreach($form->user_shares as $share)
                                    @php
                                        $member = isset($family_members) ? $family_members->firstWhere('id', $share['id']) : null;
                                        if (!$member) continue;
                                        $isPayer = $member->id == $form->paid_by_user_id;
                                    @endphp
                                    <li class="flex justify-between items-center gap-2 py-1"
                                        wire:key="share-{{ $share['id'] }}">
                                        <div class="flex items-center gap-2 sm:w-44 mr-2">
                                            <img
                                                src="{{ $member->avatar_url ?? asset('img/img_placeholder.jpg') }}"
                                                class="w-6 h-6 rounded-full inline-block"
                                                alt="" loading="lazy"
                                            >
                                            <span class="text-sm text-gray-700">{{ $member->name }}</span>
                                        </div>
                                        <p class="text-sm-medium text-gray-800">
                                            {{ number_format($share['amount'], 2, ',', ' ') }}&nbsp;{{ $this->getCurrencySymbol() }}
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <div class="w-12 h-1 bg-gray-100 rounded-full">
                                                <div
                                                    class="h-1 rounded-full {{ $isPayer ? 'bg-indigo-400' : 'bg-gray-400' }}"
                                                    style="width: {{ min($share['percentage'], 100) }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500 ml-1">{{ number_format($share['percentage'], 0) }}%</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-sm-regular">
                            Répartition : <span class="text-sm-medium">{{ __('Non définie') }}</span>
                        </p>
                    @endif
                </div>
            @endif
        </x-invoices.form.summary-item>

        <x-invoices.form.summary-item label="Dates">
            <div class="flex flex-col gap-1.5">
                @php
                    // Fonction de formatage des dates
                    function formatDate($date) {
                        if (empty($date)) {
                            return __('Non spécifiée');
                        }

                        if (is_string($date)) {
                            $date = \Carbon\Carbon::parse($date);
                        }

                        return $date->format('d/m/Y');
                    }
                @endphp

                <p class="text-sm-regular">
                    Émission: <span class="text-sm-medium">
                        {{ formatDate($form->issued_date) }}
                    </span>
                </p>
                <p class="text-sm-regular">
                    Échéance: <span class="text-sm-medium">
                        {{ formatDate($form->payment_due_date) }}
                    </span>
                </p>
                <p class="text-sm-regular">
                    Rappel: <span class="text-sm-medium">
                        {{ formatDate($form->payment_reminder) }}
                    </span>
                </p>

                @php
                    $frequencyEnum = $form->payment_frequency instanceof \App\Enums\PaymentFrequencyEnum
                        ? $form->payment_frequency
                        : \App\Enums\PaymentFrequencyEnum::tryFrom($form->payment_frequency ?? '');
                @endphp
                @if($frequencyEnum)
                    <p class="text-sm-regular">
                        Fréquence: <span class="text-sm-medium">{{ $frequencyEnum->label() }}</span>
                    </p>
                @endif
            </div>
        </x-invoices.form.summary-item>

        <x-invoices.form.summary-item label="Statut de paiement">
            @php
                $paymentStatusInstance = $form->payment_status instanceof \App\Enums\PaymentStatusEnum
                    ? $form->payment_status
                    : \App\Enums\PaymentStatusEnum::tryFrom($form->payment_status ?? '');
            @endphp

            @if($paymentStatusInstance)
                <span class="mb-2 px-2 inline-flex text-xs leading-5 rounded-full bg-gray-100 text-gray-800">
                    {{ $paymentStatusInstance->emoji() }}&nbsp;&nbsp;{{ $paymentStatusInstance->label() }}
                </span>
            @else
                <span class="mb-2 px-2 inline-flex text-xs leading-5 rounded-full bg-gray-100 text-gray-800">
                    {{ __('Non spécifié') }}
                </span>
            @endif

            <div class="mt-1 ml-1 flex flex-col gap-1.5">
                <p class="text-sm-regular">
                    Méthode: <span class="text-sm-medium">
                        @php
                            $paymentMethodInstance = $form->payment_method instanceof \App\Enums\PaymentMethodEnum
                                ? $form->payment_method
                                : \App\Enums\PaymentMethodEnum::tryFrom($form->payment_method ?? '');
                        @endphp

                        {{ $paymentMethodInstance?->label() ?? 'Non spécifiée' }}
                    </span>
                </p>
                <p class="text-sm-regular">
                    Priorité: <span class="text-sm-medium">
                        @php
                            $priorityInstance = $form->priority instanceof \App\Enums\PriorityEnum
                                ? $form->priority
                                : \App\Enums\PriorityEnum::tryFrom($form->priority ?? '');
                        @endphp

                        {{ $priorityInstance?->label() ?? 'Non spécifiée' }}
                    </span>
                </p>
            </div>
        </x-invoices.form.summary-item>

        <x-invoices.form.summary-item label="Notes" :alternateBackground="true">
            @if(!empty($form->notes))
                <span class="text-sm-regular">{{ $form->notes }}</span>
            @else
                <span class="text-sm-medium">{{ __('Aucune') }}</span>
            @endif
        </x-invoices.form.summary-item>

        <x-invoices.form.summary-item label="Tags">
            @if(empty($form->tags))
                <span class="text-sm-medium">{{ __('Aucun') }}</span>
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
        </x-invoices.form.summary-item>
    </dl>
</div>
