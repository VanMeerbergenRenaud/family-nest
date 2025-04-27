<div>
    <h2 role="heading" aria-level="2" class="sr-only">Factures de {{ auth()->user()->name }}</h2>

    @if($invoices->isEmpty())
        <div class="w-full mx-auto bg-white rounded-3xl border border-slate-200">
            <div class="relative px-8 pb-8 flex-center flex-col text-center overflow-hidden">
                <div class="absolute -top-10 w-full h-100 pointer-events-none" style="background-image: url('{{ asset('img/empty-invoice.png') }}'); background-size: contain; background-position: center; background-repeat: no-repeat;"></div>
                <!-- Textes -->
                <h3 role="heading" aria-level="3" class="text-2xl font-semibold text-gray-900 mt-46 mb-4">
                    Aucune facture créée jusqu'à présent
                </h3>

                <p class="text-gray-500 mb-8 max-w-md mx-auto">
                    Vous n'avez pas encore créé de facture. Pour commencer, cliquez sur le bouton ci-dessous pour ajouter votre première facture sans plus attendre.
                </p>

                <!-- Buttons -->
                <div class="flex-center flex-wrap gap-2">
                    <a href="{{ route('invoices.create') }}" class="button-tertiary">
                        <x-svg.add2 class="text-white"/>
                        Ajouter ma première facture
                    </a>

                    @if($archivedInvoices->isNotEmpty())
                        <a href="{{ route('invoices.archived') }}" class="button-secondary">
                            <x-svg.archive class="text-white"/>
                            Voir mes archives
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @else
        {{-- Section de navigation par dossiers --}}
        <section class="mb-8">
            <h3 role="heading" aria-level="3" class="pl-4 font-semibold text-gray-800 dark:text-white mb-3">
                Catégories
            </h3>

            <div class="flex overflow-x-scroll gap-4 scrollbar-hidden">
                {{-- Favoris --}}
                <button wire:click.prevent="openFolder('favorites', 'Favoris')"
                        class="inline-block p-5 pb-4 min-w-52 rounded-xl bg-gray-100 dark:bg-gray-800">
                    <div class="flex flex-col text-left">
                        <div class="mb-3.5 p-3 rounded-lg w-fit bg-gray-200 dark:bg-green-800">
                            <x-svg.folder class="w-6 h-6 text-green-500 dark:text-green-400"/>
                        </div>
                        <span class="text-md-medium text-gray-900 dark:text-white">Favoris</span>
                        <span class="text-sm-medium text-gray-500 dark:text-gray-400 mt-1">
                            {{ $folderStats['favorites']['count'] }} Fichiers • {{ number_format($folderStats['favorites']['amount'], 2, ',', '.') }}&nbsp;{{ $folderStats['favorites']['currency'] }}
                        </span>
                    </div>
                </button>

                {{-- Payés --}}
                <button wire:click.prevent="openFolder('paid', 'Factures payées')"
                        class="inline-block p-5 pb-4 min-w-52 rounded-xl bg-green-100 dark:bg-green-900">
                    <div class="flex flex-col text-left">
                        <div class="mb-3.5 p-3 rounded-lg w-fit bg-green-200 dark:bg-green-800">
                            <x-svg.validate class="w-6 h-6 text-green-500 dark:text-green-400"/>
                        </div>
                        <span class="text-md-medium text-gray-900 dark:text-white">Payés</span>
                        <span class="text-sm-medium text-gray-500 dark:text-gray-400 mt-1">
                            {{ $folderStats['paid']['count'] }} Fichiers • {{ number_format($folderStats['paid']['amount'], 2, ',', '.') }}&nbsp;{{ $folderStats['paid']['currency'] }}
                        </span>
                    </div>
                </button>

                {{-- Impayés --}}
                <button wire:click.prevent="openFolder('unpaid', 'Factures impayées')"
                        class="inline-block p-5 pb-4 min-w-52 rounded-xl bg-red-50 dark:bg-red-900">
                    <div class="flex flex-col text-left">
                        <div class="mb-3.5 p-3 rounded-lg w-fit bg-red-100 dark:bg-red-900">
                            <x-svg.clock class="w-6 h-6 text-red-500 dark:text-red-400"/>
                        </div>
                        <span class="text-md-medium text-gray-900 dark:text-white">Impayés</span>
                        <span class="text-sm-medium text-gray-500 dark:text-gray-400 mt-1">
                            {{ $folderStats['unpaid']['count'] }} Fichiers • {{ number_format($folderStats['unpaid']['amount'], 2, ',', '.') }}&nbsp;{{ $folderStats['unpaid']['currency'] }}
                        </span>
                    </div>
                </button>

                {{-- Retard de paiement --}}
                <button wire:click.prevent="openFolder('late', 'Retards de paiement')"
                        class="inline-block p-5 pb-4 min-w-52 rounded-xl bg-yellow-50 dark:bg-yellow-900">
                    <div class="flex flex-col text-left">
                        <div class="mb-3.5 p-3 rounded-lg w-fit bg-yellow-100 dark:bg-yellow-900">
                            <x-svg.document class="w-6 h-6 text-yellow-500 dark:text-yellow-400"/>
                        </div>
                        <span class="text-md-medium text-gray-900 dark:text-white">Retard de paiement</span>
                        <span class="text-sm-medium text-gray-500 dark:text-gray-400 mt-1">
                            {{ $folderStats['late']['count'] }} Fichiers • {{ number_format($folderStats['late']['amount'], 2, ',', '.') }}&nbsp;{{ $folderStats['late']['currency'] }}
                        </span>
                    </div>
                </button>

                {{-- Priorité élevée --}}
                <button wire:click.prevent="openFolder('high_priority', 'Priorités élevées')"
                        class="inline-block p-5 pb-4 min-w-52 rounded-xl bg-orange-50 dark:bg-orange-900">
                    <div class="flex flex-col text-left">
                        <div class="mb-3.5 p-3 rounded-lg w-fit bg-orange-100 dark:bg-orange-900">
                            <x-svg.download class="w-6 h-6 text-orange-500 dark:text-orange-400"/>
                        </div>
                        <span class="text-md-medium text-gray-900 dark:text-white">Priorité élevée</span>
                        <span class="text-sm-medium text-gray-500 dark:text-gray-400 mt-1">
                            {{ $folderStats['high_priority']['count'] }} Fichiers • {{ number_format($folderStats['high_priority']['amount'], 2, ',', '.') }}&nbsp;{{ $folderStats['high_priority']['currency'] }}
                        </span>
                    </div>
                </button>

                {{-- Semaine dernière --}}
                <button wire:click.prevent="openFolder('last_week', 'Semaine dernière')"
                        class="inline-block p-5 pb-4 min-w-52 rounded-xl bg-blue-50 dark:bg-blue-900">
                    <div class="flex flex-col text-left">
                        <div class="mb-3.5 p-3 rounded-lg w-fit bg-blue-100 dark:bg-blue-900">
                            <x-svg.clock class="w-6 h-6 text-blue-500 dark:text-blue-400"/>
                        </div>
                        <span class="text-md-medium text-gray-900 dark:text-white">Semaine dernière</span>
                        <span class="text-sm-medium text-gray-500 dark:text-gray-400 mt-1">
                            {{ $folderStats['last_week']['count'] }} Fichiers • {{ number_format($folderStats['last_week']['amount'], 2, ',', '.') }}&nbsp;{{ $folderStats['late']['currency'] }}
                        </span>
                    </div>
                </button>

                {{-- Semaine prochaine --}}
                @if($archivedInvoices->isNotEmpty())
                    <a href="{{ route('invoices.archived') }}"  class="inline-block p-5 pb-4 min-w-52 rounded-xl bg-teal-100 dark:bg-teal-900">
                        <div class="flex flex-col text-left">
                            <div class="mb-3.5 p-3 rounded-lg w-fit bg-teal-200 dark:bg-teal-900">
                                <x-svg.archive class="w-6 h-6 text-teal-500 dark:text-teal-400"/>
                            </div>
                            <span class="text-md-medium text-gray-900 dark:text-white">Archives</span>
                            <span class="text-sm-medium text-gray-500 dark:text-gray-400 mt-1">
                                {{ $archivedInvoices->count() }} Fichiers
                            </span>
                        </div>
                    </a>
                @endif
            </div>
        </section>

        {{-- Section des fichiers récents --}}
        <section class="mb-10">
            <h3 role="heading" aria-level="3" class="pl-4 font-semibold text-gray-800 dark:text-white mb-3">
                Factures récentes
            </h3>

            @if($recentInvoices->isEmpty())
                <div class="p-6 bg-white dark:bg-gray-800 rounded-xl w-full border border-slate-200">
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Aucune facture récente.') }}</p>
                </div>
            @else
                <ul class="flex overflow-x-scroll gap-4 scrollbar-hidden">
                    @foreach($recentInvoices as $invoice)
                        <li wire:key="invoice-{{ $invoice->id }}"
                            class="pl-4 py-4 pr-3 min-w-fit h-fit rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
                            @php
                                $extension = $invoice->file->file_extension ?? null;
                            @endphp
                            <div class="flex items-center justify-between gap-4">
                                <div wire:click="showInvoiceModal({{ $invoice->id }})" class="cursor-pointer bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 p-2 rounded-lg">
                                    @if(View::exists('components.svg.file.' . $extension))
                                        <x-dynamic-component :component="'svg.file.' . $extension" class="w-6 h-6"/>
                                    @else
                                        <x-svg.file.default class="w-6 h-6"/>
                                    @endif
                                </div>
                                <div>
                                    <h4 role="heading" aria-level="4" class="text-sm-medium text-gray-900 dark:text-white">
                                        {{ Str::limit($invoice->name, 16) }}
                                    </h4>
                                    <p class="mt-1 w-max text-xs-regular text-gray-500 dark:text-gray-400">
                                        {{ $invoice->dateForHumans($invoice->created_at) }}
                                        • {{ $invoice->amount ?? 'Montant vide' }} {{ $this->getInvoiceCurrencySymbol($invoice) }}
                                    </p>
                                </div>
                                {{-- Menu d'action --}}
                                <x-invoices.menu-actions :$invoice :dotsRotation="true" />
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Tableau regroupant toutes les factures --}}
        <section class="w-full overflow-hidden bg-white dark:bg-gray-800 rounded-2xl border border-slate-200">

            {{-- En-tête --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 pl-6 border-b border-gray-200 dark:border-gray-700 dark:bg-gray-800">
                <h3 role="heading" aria-level="3" class="text-md-semibold mb-3 sm:mb-0 dark:text-white">
                    Tous les fichiers
                    <span aria-hidden="true" class="relative -top-0.5 ml-2 px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs-medium dark:bg-gray-700 dark:text-gray-200">
                       {{ \Illuminate\Support\Number::format($invoices->total()) }}
                   </span>
                </h3>
                <div class="flex flex-wrap gap-2">
                    {{-- Filtres --}}
                    <x-menu>
                        <x-menu.button class="button-primary flex items-center">
                            <x-svg.filter/>
                            {{ $activeFilter ? $availableFilters[$activeFilter] : 'Filtres' }}
                        </x-menu.button>

                        <x-menu.items class="mt-2 w-64">
                            <p class="px-2.5 py-2 text-sm-medium text-gray-700 dark:text-gray-400">Filtres</p>

                            <x-menu.divider/>

                            @php
                                $activeState = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-white';
                                $inactiveState = 'bg-white hover:bg-gray-100 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-700';
                                $iconActiveClass = 'text-gray-900 dark:text-white';
                                $iconInactiveClass = 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-200';
                            @endphp

                                <!-- Filtres de date -->
                            <x-menu.item wire:click="applyFilter('issued_date_asc')"
                                         class="{{ $activeFilter === 'issued_date_asc' ? $activeState : $inactiveState }} group">
                                <x-svg.calendar
                                    class="w-4 h-4 transition-colors duration-200 {{ $activeFilter === 'issued_date_asc' ? $iconActiveClass : $iconInactiveClass }}"/>
                                Date (ancien → récent)
                            </x-menu.item>

                            <x-menu.item wire:click="applyFilter('issued_date_desc')"
                                         class="{{ $activeFilter === 'issued_date_desc' ? $activeState : $inactiveState }} group">
                                <x-svg.calendar
                                    class="w-4 h-4 transition-colors duration-200 {{ $activeFilter === 'issued_date_desc' ? $iconActiveClass : $iconInactiveClass }}"/>
                                Date (récent → ancien)
                            </x-menu.item>

                            <x-menu.divider/>

                            <!-- Filtres d'échéance -->
                            <x-menu.item wire:click="applyFilter('payment_due_date_asc')"
                                         class="{{ $activeFilter === 'payment_due_date_asc' ? $activeState : $inactiveState }} group">
                                <x-svg.clock
                                    class="w-4 h-4 transition-colors duration-200 {{ $activeFilter === 'payment_due_date_asc' ? $iconActiveClass : $iconInactiveClass }}"/>
                                Échéance (proche → loin)
                            </x-menu.item>

                            <x-menu.item wire:click="applyFilter('payment_due_date_desc')"
                                         class="{{ $activeFilter === 'payment_due_date_desc' ? $activeState : $inactiveState }} group">
                                <x-svg.clock
                                    class="w-4 h-4 transition-colors duration-200 {{ $activeFilter === 'payment_due_date_desc' ? $iconActiveClass : $iconInactiveClass }}"/>
                                Échéance (loin → proche)
                            </x-menu.item>

                            <x-menu.divider/>

                            <!-- Filtres de montant -->
                            <x-menu.item wire:click="applyFilter('amount_asc')"
                                         class="{{ $activeFilter === 'amount_asc' ? $activeState : $inactiveState }} group">
                                <x-svg.dollar
                                    class="w-4 h-4 transition-colors duration-200 {{ $activeFilter === 'amount_asc' ? $iconActiveClass : $iconInactiveClass }}"/>
                                Prix (- → +)
                            </x-menu.item>

                            <x-menu.item wire:click="applyFilter('amount_desc')"
                                         class="{{ $activeFilter === 'amount_desc' ? $activeState : $inactiveState }} group">
                                <x-svg.dollar
                                    class="w-4 h-4 transition-colors duration-200 {{ $activeFilter === 'amount_desc' ? $iconActiveClass : $iconInactiveClass }}"/>
                                Prix (+ → -)
                            </x-menu.item>

                            <x-menu.divider/>

                            <!-- Filtres alphabétiques -->
                            <x-menu.item wire:click="applyFilter('name_asc')"
                                         class="{{ $activeFilter === 'name_asc' ? $activeState : $inactiveState }} group">
                                <x-svg.atoz
                                    class="w-4 h-4 transition-colors duration-200 {{ $activeFilter === 'name_asc' ? $iconActiveClass : $iconInactiveClass }}"/>
                                A → Z
                            </x-menu.item>

                            <x-menu.item wire:click="applyFilter('name_desc')"
                                         class="{{ $activeFilter === 'name_desc' ? $activeState : $inactiveState }} group">
                                <x-svg.atoz
                                    class="w-4 h-4 rotate-180 transition-colors duration-200 {{ $activeFilter === 'name_desc' ? $iconActiveClass : $iconInactiveClass }}"/>
                                Z → A
                            </x-menu.item>

                            @if($activeFilter)
                                <x-menu.divider/>

                                <div>
                                    <x-menu.item wire:click="resetSort"
                                                 class="flex items-center text-sm-medium text-slate-800 hover:bg-slate-100 transition-colors rounded">
                                        <x-svg.reset/>
                                        Réinitialiser
                                    </x-menu.item>
                                </div>
                            @endif
                        </x-menu.items>
                    </x-menu>

                    {{-- Colonnes --}}
                    <x-menu>
                        <x-menu.button class="button-primary">
                            <x-svg.columns/>
                            Colonnes
                        </x-menu.button>

                        <x-menu.items class="mt-2 w-56">
                            <p class="px-2.5 py-2 text-sm-medium text-gray-700 dark:text-gray-400">
                                Colonnes à afficher
                            </p>

                            <x-menu.divider/>

                            {{-- Boucle sur toutes les colonnes disponibles pour un code plus propre --}}
                            @php
                                $columnLabels = [
                                    'name' => 'Nom du fichier',
                                    'type' => 'Type',
                                    'category' => 'Catégorie',
                                    'issuer_name' => 'Émetteur',
                                    'amount' => 'Montant',
                                    'payment_status' => 'Statut de paiement',
                                    'issued_date' => 'Date d‘émission',
                                    'payment_due_date' => 'Date d‘échéance',
                                    'tags' => 'Tags',
                                ];
                            @endphp

                            @foreach($columnLabels as $columnKey => $columnLabel)
                                <x-menu.item>
                                    <x-form.checkbox-input
                                        label="{{ $columnLabel }}"
                                        name="column_name_{{ $columnKey }}"
                                        wire:model.live="visibleColumns.{{ $columnKey }}"
                                        checked="{{ $this->isColumnVisible($columnKey) ? 'checked' : '' }}"
                                    />
                                </x-menu.item>
                            @endforeach

                            <x-menu.divider/>

                            <div>
                                <x-menu.item wire:click="resetColumns" class="flex items-center text-sm-medium text-slate-800 hover:bg-slate-100 transition-colors rounded">
                                    <x-svg.reset/>
                                    Réinitialiser
                                </x-menu.item>
                            </div>
                        </x-menu.items>
                    </x-menu>

                    {{-- Télécharger tout --}}
                    <button wire:click="downloadAllFiles" class="button-tertiary">
                        <x-svg.download class="text-white"/>
                        Télécharger tout
                    </button>
                </div>
            </div>

            <div class="w-full overflow-x-auto scrollbar-hidden">
                <table class="w-full" aria-labelledby="tableTitle">
                    <thead>
                    <tr>
                        {{-- Checkboxes... --}}
                        <td class="p-0 pl-2">
                            <x-invoices.index.check-all />
                        </td>
                        {{-- Colonne "Nom du fichier" (toujours visible) --}}
                        @if($visibleColumns['name'] ?? false)
                            <th scope="col" class="min-w-[10rem]">
                                <button wire:click="sortBy('name')" class="flex items-center">
                                    <span>Nom de la facture</span>
                                    <x-invoices.index.sortable field="name" :$sortField :$sortDirection />
                                </button>
                            </th>
                        @endif

                        {{-- Type --}}
                        @if($visibleColumns['type'] ?? false)
                            <th scope="col" class="min-w-[10rem]">
                                <button wire:click="sortBy('type')" class="flex items-center">
                                    <span>Type</span>
                                    <x-invoices.index.sortable field="type" :$sortField :$sortDirection />
                                </button>
                            </th>
                        @endif

                        {{-- Catégorie --}}
                        @if($visibleColumns['category'] ?? false)
                            <th scope="col" class="min-w-[10rem]">
                                <button wire:click="sortBy('category')" class="flex items-center">
                                    <span>Catégorie</span>
                                    <x-invoices.index.sortable field="category" :$sortField :$sortDirection />
                                </button>
                            </th>
                        @endif

                        {{-- Émetteur --}}
                        @if($visibleColumns['issuer_name'] ?? false)
                            <th scope="col" class="min-w-[10rem]">
                                <button wire:click="sortBy('issuer_name')" class="flex items-center">
                                    <span>Émetteur</span>
                                    <x-invoices.index.sortable field="issuer_name" :$sortField :$sortDirection />
                                </button>
                            </th>
                        @endif

                        {{-- Montant --}}
                        @if($visibleColumns['amount'] ?? false)
                            <th scope="col" class="min-w-[10rem]">
                                <button wire:click="sortBy('amount')" class="flex items-center">
                                    <span>Montant</span>
                                    <x-invoices.index.sortable field="amount" :$sortField :$sortDirection />
                                </button>
                            </th>
                        @endif

                        {{-- Statut de paiement --}}
                        @if($visibleColumns['payment_status'] ?? false)
                            <th scope="col" class="min-w-[10rem]">
                                <button wire:click="sortBy('payment_status')" class="flex items-center">
                                    <span>Statut</span>
                                    <x-invoices.index.sortable field="payment_status" :$sortField :$sortDirection />
                                </button>
                            </th>
                        @endif

                        {{-- Date d'émission --}}
                        @if($visibleColumns['issued_date'] ?? false)
                            <th scope="col" class="min-w-[12.5rem]">
                                <button wire:click="sortBy('issued_date')" class="flex items-center">
                                    <span>Date d'émission</span>
                                    <x-invoices.index.sortable field="issued_date" :$sortField :$sortDirection />
                                </button>
                            </th>
                        @endif

                        {{-- Date d'échéance --}}
                        @if($visibleColumns['payment_due_date'] ?? false)
                            <th scope="col" class="min-w-[12.5rem]">
                                <button wire:click="sortBy('payment_due_date')" class="flex items-center">
                                    <span>Date d'échéance</span>
                                    <x-invoices.index.sortable field="payment_due_date" :$sortField :$sortDirection />
                                </button>
                            </th>
                        @endif

                        {{-- Tags --}}
                        @if($visibleColumns['tags'] ?? false)
                            <th scope="col" class="min-w-[12.5rem]">
                                Tags associés
                            </th>
                        @endif

                        {{-- Actions (toujours visible) --}}
                        <th scope="col" class="text-right">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($invoices as $invoice)
                        <tr wire:key="invoice-{{ $invoice->id }}" class="relative pl-8 group hover:bg-gray-50/70">
                            {{-- Checkbox --}}
                            <td class="p-0 pl-2">
                                <label class="relative flex items-center cursor-pointer whitespace-nowrap p-4 w-fit">
                                    <input wire:model="selectedInvoiceIds" value="{{ $invoice->id }}" type="checkbox" class="peer h-4 w-4 cursor-pointer transition-all appearance-none rounded border border-slate-300 checked:bg-blue-700 checked:border-blue-700" />
                                    <span class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none">
                                        <x-svg.check class="text-white h-3 w-3" />
                                    </span>
                                </label>
                            </td>

                            {{-- Nom du fichier --}}
                            @if($visibleColumns['name'] ?? false)
                                <td>
                                    {{-- Affichage du nom du fichier --}}
                                    <div class="flex items-center cursor-pointer" wire:click="showInvoiceModal({{ $invoice->id }})">
                                        @php
                                            $extension = $invoice->file->file_extension ?? null;
                                        @endphp

                                        <div class="mr-3 p-2 pl-0 rounded">
                                            @if(View::exists('components.svg.file.' . $extension))
                                                <x-dynamic-component :component="'svg.file.' . $extension" class="w-6 h-6"/>
                                            @else
                                                <x-svg.file.default class="w-6 h-6"/>
                                            @endif
                                        </div>

                                        <div class="flex flex-col">
                                            <span class="text-sm-medium text-gray-900 dark:text-gray-400">{{ $invoice->name }}</span>
                                        </div>
                                    </div>
                                </td>
                            @endif

                            {{-- Type --}}
                            @if($visibleColumns['type'] ?? false)
                                <td>
                                    {{ $invoice->type ?? 'Non spécifié' }}
                                </td>
                            @endif

                            {{-- Catégorie --}}
                            @if($visibleColumns['category'] ?? false)
                                <td>
                                    {{ $invoice->category ?? 'Non spécifiée' }}
                                </td>
                            @endif

                            {{-- Émetteur --}}
                            @if($visibleColumns['issuer_name'] ?? false)
                                <td>
                                    {{ $invoice->issuer_name ?? 'Non spécifié' }}
                                </td>
                            @endif

                            {{-- Montant --}}
                            @if($visibleColumns['amount'] ?? false)
                                <td>
                                    {{ number_format($invoice->amount, 2, ',', ' ') ?? 'Non spécifié' }} {{ $invoice->symbol ?? '€' }}
                                </td>
                            @endif

                            {{-- Statut de paiement --}}
                            @if($visibleColumns['payment_status'] ?? false)
                                <td>
                                    @php
                                        $statusEnum = $invoice->payment_status;
                                        $statusColor = $statusEnum?->color() ?? 'gray';
                                        $statusClass = "bg-$statusColor-100 text-$statusColor-800";
                                        $statusText = $statusEnum?->label() ?? 'Non spécifié';
                                        $statusEmoji = $statusEnum?->emoji() ?? '';
                                    @endphp
                                    <span class="px-3 py-1 {{ $statusClass }} rounded-full text-xs-medium">
                                        {{ $statusEmoji }}&nbsp;&nbsp;{{ $statusText }}
                                    </span>
                                </td>
                            @endif

                            {{-- Date d'émission --}}
                            @if($visibleColumns['issued_date'] ?? false)
                                <td>
                                    @if($invoice->issued_date)
                                        {{ $invoice->dateForHumans($invoice->issued_date) }}
                                    @else
                                        {{ __('Non spécifiée') }}
                                    @endif
                                </td>
                            @endif

                            {{-- Date d'échéance --}}
                            @if($visibleColumns['payment_due_date'] ?? false)
                                <td>
                                    @if($invoice->payment_due_date)
                                        {{ $invoice->dateForHumans($invoice->payment_due_date) }}
                                    @else
                                        {{ __('Non spécifiée') }}
                                    @endif
                                </td>
                            @endif

                            {{-- Tags --}}
                            @if($visibleColumns['tags'] ?? false)
                                <td>
                                    <div class="flex flex-wrap gap-1 lg:min-w-max">
                                        @if($invoice->tags)
                                            @php
                                                $tags = is_array($invoice->tags)
                                                    ? $invoice->tags
                                                    : json_decode($invoice->tags, true) ?? [];
                                                $visibleTags = array_slice($tags, 0, 3);
                                                $remainingCount = count($tags) - count($visibleTags);
                                            @endphp

                                            @foreach($visibleTags as $tag)
                                                <span
                                                    class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs-medium whitespace-nowrap">
                                                {{ $tag }}
                                            </span>
                                            @endforeach

                                            @if($remainingCount > 0)
                                                <span
                                                    class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs-medium whitespace-nowrap">
                                                +{{ $remainingCount }}
                                            </span>
                                            @endif
                                        @else
                                            <span class="text-sm-regular text-gray-400">Aucun tag</span>
                                        @endif
                                    </div>
                                </td>
                            @endif

                            {{-- Actions --}}
                            <td class="text-right">
                                <div class="flex justify-end">
                                    <x-invoices.menu-actions :$invoice />
                                </div>
                            </td>
                        </tr>

                        <!-- Bulk actions -->
                        <x-dashboard.bulk-actions :$invoices />
                    @endforeach
                    @if($invoices->count() <= 5)
                        <tr>
                            <td colspan="100%" class="h-74">
                                <p class="sr-only">Espace vide</p>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            @if($invoices->hasPages())
                <div class="py-2 px-4 border-t border-slate-200">
                    {{ $invoices->links() }}
                </div>
            @endif
        </section>

        <!-- Modal pour afficher les factures d'un dossier spécifique avec design de cartes -->
        @if($showFolderModal)
            <x-modal wire:model="showFolderModal">
                <x-modal.panel class="max-w-5xl bg-white dark:bg-gray-900">
                    <!-- En-tête avec titre et compteur -->
                    <div class="sticky top-0 px-6 py-5 flex items-center justify-between border-b border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 z-20">
                        <div class="flex flex-wrap items-center gap-3 pr-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $folderTitle }}</h2>
                            <span
                                class="inline-flex px-2.5 py-1 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-full">
                                {{ count($folderInvoices) }} factures
                            </span>
                        </div>
                    </div>

                    <!-- Contenu principal -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-950 overflow-y-auto min-h-[40vh] max-h-[60vh]">
                        @if($folderInvoices->isEmpty())
                            <!-- État vide -->
                            <div class="flex-center flex-col py-16 rounded-lg bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
                                <x-svg.document class="w-14 h-14 text-gray-300 dark:text-gray-700 mb-3"/>
                                <p class="text-center px-4 text-md-medium text-gray-700 dark:text-gray-300">{{ __('Aucune facture dans cette catégorie') }}</p>
                                <p class="text-center px-4 text-sm text-gray-500 dark:text-gray-500 mt-1">{{ __('Les factures que vous ajouterez apparaîtront ici !') }}</p>
                            </div>
                        @else
                            <!-- Liste des factures -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                @foreach($folderInvoices as $invoice)
                                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-slate-200 dark:border-gray-800 hover:shadow-xs transition-shadow duration-200 overflow-hidden">
                                        <!-- En-tête de la carte -->
                                        <div class="px-4 py-3 dark:border-gray-800 flex justify-between items-center">
                                            @php
                                                $extension = $invoice->file->file_extension ?? null;
                                            @endphp

                                            <div class="flex items-center gap-2.5 overflow-hidden pr-2">
                                                <div class="flex-shrink-0 p-1.5 rounded">
                                                    @if(View::exists('components.svg.file.' . $extension))
                                                        <x-dynamic-component :component="'svg.file.' . $extension" class="w-5 h-5"/>
                                                    @else
                                                        <x-svg.file.default class="w-5 h-5"/>
                                                    @endif
                                                </div>
                                                <h3 class="font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $invoice->name }}
                                                </h3>
                                            </div>

                                            <x-invoices.menu-actions :$invoice />
                                        </div>

                                        <!-- Corps de la carte -->
                                        <div class="p-4 border-t border-b border-slate-100">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="space-y-1">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Date d\'émission') }}</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $invoice->dateForHumans($invoice->issued_date) }}
                                                    </p>
                                                </div>
                                                <div class="space-y-1">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Montant') }}</p>
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                        {{ $this->formatAmount($invoice->amount, $invoice->currency) }}
                                                    </p>
                                                </div>
                                                <div class="space-y-1">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Date d‘échéance') }}</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $invoice->dateForHumans($invoice->payment_due_date) }}
                                                    </p>
                                                </div>
                                                <div class="space-y-1">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Émetteur') }}</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate"
                                                       title="{{ $invoice->issuer_name ?? 'Non spécifié' }}">
                                                        {{ $invoice->issuer_name ?? 'Non spécifié' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Pied de la carte -->
                                        <div class="dark:border-gray-800 px-4 py-3 flex justify-between items-center">
                                            @php
                                                $statusEnum = $invoice->payment_status;
                                                $statusColor = $statusEnum?->color() ?? 'gray';
                                                $statusClass = "bg-$statusColor-100 text-$statusColor-800";
                                                $statusText = $statusEnum?->label() ?? 'Non spécifié';
                                                $statusEmoji = $statusEnum?->emoji() ?? '';
                                            @endphp
                                            <span class="px-3 py-1 {{ $statusClass }} rounded-full text-xs-medium">
                                                {{ $statusEmoji }}&nbsp;&nbsp;{{ $statusText }}
                                            </span>


                                            <div class="flex gap-1">
                                                <button wire:click="showInvoiceModal({{ $invoice->id }})"
                                                        class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                                        title="Voir">
                                                    <x-svg.show class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
                                                </button>

                                                @can('update', $invoice)
                                                    <a href="{{ route('invoices.edit', $invoice->id) }}"
                                                       wire:navigate
                                                       class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                                       title="Aller vers la page d'édition">
                                                        <x-svg.edit class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Pied de modal -->
                    <x-modal.footer
                        class="bg-white dark:bg-gray-900 border-t border-slate-200 dark:border-gray-800 px-6 py-4">
                        <div class="flex items-center justify-between w-full">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ count($folderInvoices) }} {{ __('factures trouvées') }}
                            </div>
                            <x-modal.close>
                                <button type="button"
                                        class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium transition-colors">
                                    {{ __('Fermer') }}
                                </button>
                            </x-modal.close>
                        </div>
                    </x-modal.footer>
                </x-modal.panel>
            </x-modal>
        @endif

        @if($showInvoicePreviewModal)
            <x-modal wire:model="showInvoicePreviewModal">
                <x-modal.panel class="max-w-4xl">
                    <p class="sticky top-0 p-5 px-8 max-w-full text-xl-bold bg-white dark:bg-gray-800 dark:border-gray-700 z-20">
                        {{ __('Aperçu de la facture') }}
                    </p>

                    <div class="p-1 border-b border-gray-200 bg-gray-50 dark:bg-gray-900 relative overflow-auto">
                        <div wire:loading.remove>
                            <x-file-viewer
                                :filePath="$filePath"
                                :fileExtension="$fileExtension"
                                :fileName="$fileName"
                                class="min-h-[60vh] max-h-[75vh]"
                            />
                        </div>
                    </div>

                    <x-modal.footer class="bg-white dark:bg-gray-800 border-t border-gray-400 dark:border-gray-700">
                        <div class="flex justify-between w-full">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-medium">Type:</span>
                                {{ strtoupper($fileExtension) ?? 'Inconnu' }}
                            </p>
                            <x-modal.close>
                                <button type="button" class="button-secondary">
                                    {{ __('Fermer') }}
                                </button>
                            </x-modal.close>
                        </div>
                    </x-modal.footer>
                </x-modal.panel>
            </x-modal>
        @endif
    @endif

    @if($showSidebarInvoiceDetails)
        <x-modal wire:model="showSidebarInvoiceDetails">
            <x-modal.panel position="center-right" height="97vh">
                <p class="sticky top-0 p-5 pr-15 max-w-full text-xl-bold border-b border-slate-200 bg-white dark:bg-gray-800 dark:border-gray-700 z-20">
                    {{ __('Résumé des informations de la facture') }}
                </p>

                <div class="p-4">
                    <x-invoices.create.summary :form="$this->invoice" :$family_members />
                </div>

                <x-modal.footer class="bg-white dark:bg-gray-800 border-t border-gray-400 dark:border-gray-700">
                    <div class="flex justify-end w-full gap-3">
                        <x-modal.close>
                            <button type="button" class="button-secondary">
                                {{ __('Fermer') }}
                            </button>
                        </x-modal.close>
                    </div>
                </x-modal.footer>
            </x-modal.panel>
        </x-modal>
    @endif

    @if($showDeleteFormModal)
        <x-modal wire:model="showDeleteFormModal">
            <x-modal.panel>
                <form wire:submit.prevent="deleteDefinitelyInvoice">
                    @csrf

                    <div x-data="{ confirmation: '' }">
                        <div class="flex gap-x-6 p-8">
                            <x-svg.advertising/>

                            <div>
                                <h3 role="heading" aria-level="3" class="mb-4 text-xl-semibold">
                                    {{ __('Supprimer la facture') }}
                                </h3>
                                <p class="mt-2 text-md-regular text-gray-500">
                                    {{ __('Êtes-vous sûre de vouloir supprimer la facture') }}
                                    <strong class="font-semibold"> {{ $invoice->name }}&nbsp;?</strong>
                                    {{ __('Toutes les données seront supprimées. Cette action est irréversible.') }}
                                </p>
                                <div class="mt-6 mb-2 flex flex-col gap-3">
                                    <label for="delete-definitely-invoice" class="text-sm-medium text-gray-800">
                                        {{ __('Veuillez tapper "CONFIRMER" pour confirmer la suppression.') }}
                                    </label>
                                    <input x-model="confirmation" placeholder="CONFIRMER" type="text" id="delete-definitely-invoice"
                                           class="py-2 px-3 text-sm-regular border border-gray-300 rounded-md w-[87.5%]"
                                           autofocus>
                                </div>
                            </div>
                        </div>

                        <x-modal.footer>
                            <x-modal.close>
                                <button type="button" class="button-secondary">
                                    {{ __('Annuler') }}
                                </button>
                            </x-modal.close>

                            <x-modal.close>
                                <button type="submit" class="button-danger" :disabled="confirmation !== 'CONFIRMER'">
                                    {{ __('Supprimer') }}
                                </button>
                            </x-modal.close>
                        </x-modal.footer>
                    </div>
                </form>
            </x-modal.panel>
        </x-modal>
    @endif
</div>
