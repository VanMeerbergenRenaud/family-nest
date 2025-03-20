<div>
    <h1 class="sr-only">Factures de {{ auth()->user()->name }}</h1>

    {{-- Section de navigation par dossiers --}}
    <section class="mb-8">
        <h2 class="pl-4 font-semibold text-gray-800 dark:text-white mb-3">Catégories</h2>

        <div class="flex <!--overflow-x-scroll--> gap-4 scrollbar-hidden">
            {{-- Favoris --}}
            <button wire:click.prevent="openFolder('favorites', 'Favoris')"
                    class="inline-block p-5 pb-4 min-w-48 rounded-xl bg-gray-100 dark:bg-gray-800">
                <div class="flex flex-col text-left">
                    <div class="mb-3.5 p-3 rounded-lg w-fit bg-gray-200 dark:bg-green-800">
                        <x-svg.folder class="w-6 h-6 text-green-500 dark:text-green-400" />
                    </div>
                    <span class="text-md-medium text-gray-900 dark:text-white">Favoris</span>
                    <span class="text-sm-medium text-gray-500 dark:text-gray-400 mt-1">
                        {{ $folderStats['favorites']['count'] }} Fichiers • {{ $folderStats['favorites']['amount'] }}€
                    </span>
                </div>
            </button>

            {{-- Payés --}}
            <button wire:click.prevent="openFolder('paid', 'Factures payées')"
               class="inline-block p-5 pb-4 min-w-48 rounded-xl bg-green-100 dark:bg-green-900">
                <div class="flex flex-col text-left">
                    <div class="mb-3.5 p-3 rounded-lg w-fit bg-green-200 dark:bg-green-800">
                        <x-svg.validate class="w-6 h-6 text-green-500 dark:text-green-400"/>
                    </div>
                    <span class="text-md-medium text-gray-900 dark:text-white">Payés</span>
                    <span class="text-sm-medium text-gray-500 dark:text-gray-400 mt-1">
                        {{ $folderStats['paid']['count'] }} Fichiers • {{ $folderStats['paid']['amount'] }}€
                    </span>
                </div>
            </button>

            {{-- Impayés --}}
            <button wire:click.prevent="openFolder('unpaid', 'Factures impayées')"
               class="inline-block p-5 pb-4 min-w-48 rounded-xl bg-red-50 dark:bg-red-900">
                <div class="flex flex-col text-left">
                    <div class="mb-3.5 p-3 rounded-lg w-fit bg-red-100 dark:bg-red-900">
                    <x-svg.clock class="w-6 h-6 text-red-500 dark:text-red-400"/>
                    </div>
                    <span class="text-md-medium text-gray-900 dark:text-white">Impayés</span>
                    <span class="text-sm-medium text-gray-500 dark:text-gray-400 mt-1">
                        {{ $folderStats['unpaid']['count'] }} Fichiers • {{ $folderStats['unpaid']['amount'] }}€
                    </span>
                </div>
            </button>

            {{-- Retard de paiement --}}
            <button wire:click.prevent="openFolder('late', 'Retards de paiement')"
               class="inline-block p-5 pb-4 min-w-48 rounded-xl bg-yellow-50 dark:bg-yellow-900">
                <div class="flex flex-col text-left">
                    <div class="mb-3.5 p-3 rounded-lg w-fit bg-yellow-100 dark:bg-yellow-900">
                    <x-heroicon-o-document class="w-6 h-6 text-yellow-500 dark:text-yellow-400"/>
                    </div>
                    <span class="text-md-medium text-gray-900 dark:text-white">Retard de paiement</span>
                    <span class="text-sm-medium text-gray-500 dark:text-gray-400 mt-1">
                        {{ $folderStats['late']['count'] }} Fichiers • {{ $folderStats['late']['amount'] }}€
                    </span>
                </div>
            </button>

            {{-- Priorité élevée --}}
            <button wire:click.prevent="openFolder('high_priority', 'Priorités élevées')"
               class="inline-block p-5 pb-4 min-w-48 rounded-xl bg-orange-50 dark:bg-orange-900">
                <div class="flex flex-col text-left">
                    <div class="mb-3.5 p-3 rounded-lg w-fit bg-orange-100 dark:bg-orange-900">
                        <x-svg.download class="w-6 h-6 text-orange-500 dark:text-orange-400"/>
                    </div>
                    <span class="text-md-medium text-gray-900 dark:text-white">Priorité élevée</span>
                    <span class="text-sm-medium text-gray-500 dark:text-gray-400 mt-1">
                        {{ $folderStats['high_priority']['count'] }} Fichiers • {{ $folderStats['high_priority']['amount'] }}€
                    </span>
                </div>
            </button>
        </div>
    </section>

    {{-- Section des fichiers récents --}}
    <section class="mb-10">
        <h2 class="pl-4 font-semibold text-gray-800 dark:text-white mb-3">Factures récentes</h2>

        <div class="flex overflow-x-scroll gap-4 scrollbar-hidden">
            @if($recentInvoices->isEmpty())
                <div class="p-6 bg-white dark:bg-gray-800 rounded-xl w-full">
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Aucune facture récente.') }}</p>
                </div>
            @else
                @foreach($recentInvoices as $invoice)
                    <div class="pl-4 py-4 pr-3 min-w-fit h-fit rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="flex items-center justify-between gap-4">
                            <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg">
                                <x-svg.file-size class="w-5 h-5 text-gray-600 dark:text-gray-400"/>
                            </div>
                            <div>
                                <h3 class="text-sm-medium text-gray-900 dark:text-white">
                                    {{ Str::limit($invoice->name, 16) }}
                                </h3>
                                <p class="mt-1 w-max text-xs-regular text-gray-500 dark:text-gray-400">
                                    {{ $invoice->created_at ? \Carbon\Carbon::parse($invoice->issued_date)->format('j M Y') : 'Date inconnue' }}
                                    • {{ $invoice->amount ?? 'Montant vide' }} {{ $invoice->currency ?? '€' }}
                                </p>
                            </div>
                            {{-- Menu d'action --}}
                            <x-menu>
                                <x-menu.button class="rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 p-1">
                                    <x-svg.dots class="w-5 h-5 text-gray-500 rotate-90"/>
                                </x-menu.button>
                                <x-menu.items>
                                    <x-menu.item wire:click="showInvoiceModal({{ $invoice->id }})">
                                        <x-svg.show class="w-4 h-4 group-hover:text-gray-900"/>
                                        {{ __('Voir l’aperçu') }}
                                    </x-menu.item>
                                    <x-menu.item wire:click="showInvoicePage({{ $invoice->id }})">
                                        <x-svg.help class="w-4 h-4 group-hover:text-gray-900"/>
                                        {{ __('Voir en détail') }}
                                    </x-menu.item>
                                    <x-menu.divider />
                                    @if(!$invoice->is_favorite)
                                        <x-menu.item wire:click="toggleFavorite({{ $invoice->id }})">
                                            <x-svg.star class="w-4 h-4 group-hover:text-gray-900"/>
                                            {{ __('Mettre en favori') }}
                                        </x-menu.item>
                                    @endif
                                    <x-menu.item wire:click="downloadInvoice({{ $invoice->id }})">
                                        <x-svg.download class="w-4 h-4 group-hover:text-gray-900"/>
                                        {{ __('Télécharger') }}
                                    </x-menu.item>
                                    <x-menu.item wire:click="showEditPage({{ $invoice->id }})">
                                        <x-svg.edit class="w-4 h-4" class="group-hover:text-gray-900"/>
                                        {{ __('Modifier') }}
                                    </x-menu.item>
                                    <x-menu.item wire:click="showArchiveForm({{ $invoice->id }})" class="group hover:text-red-500">
                                        <x-svg.trash class="w-4 h-4 group-hover:text-red-500"/>
                                        {{ __('Archiver') }}
                                    </x-menu.item>
                                </x-menu.items>
                            </x-menu>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    {{-- Tableau regroupant toutes les factures --}}
    <section class="w-full overflow-hidden bg-white dark:bg-gray-800 rounded-2xl shadow-sm">

        {{-- En-tête --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 border-b border-gray-200 dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-md-semibold mb-3 sm:mb-0 dark:text-white">Tous les fichiers</h2>
            <div class="flex flex-wrap gap-2">
                {{-- Filtres --}}
                <x-menu>
                    <x-menu.button class="button-primary flex items-center">
                        <x-svg.filter/>
                        {{ $activeFilter ? $availableFilters[$activeFilter] : 'Filtres' }}
                    </x-menu.button>

                    <x-menu.items class="mt-2 w-64">
                        <p class="px-2.5 py-2 text-sm-medium text-gray-700 dark:text-gray-400">Filtres</p>

                        <x-menu.divider />

                        @php
                            $activeState = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-white';
                            $inactiveState = 'bg-white hover:bg-gray-100 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-700';
                            $iconActiveClass = 'text-gray-900 dark:text-white';
                            $iconInactiveClass = 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-200';
                        @endphp

                        <!-- Filtres de date -->
                        <x-menu.item wire:click="applyFilter('issued_date_asc')" class="{{ $activeFilter === 'issued_date_asc' ? $activeState : $inactiveState }} group">
                            <x-svg.calendar class="w-4 h-4 transition-colors duration-200 {{ $activeFilter === 'issued_date_asc' ? $iconActiveClass : $iconInactiveClass }}"/>
                            Date (ancien → récent)
                        </x-menu.item>

                        <x-menu.item wire:click="applyFilter('issued_date_desc')" class="{{ $activeFilter === 'issued_date_desc' ? $activeState : $inactiveState }} group">
                            <x-svg.calendar class="w-4 h-4 transition-colors duration-200 {{ $activeFilter === 'issued_date_desc' ? $iconActiveClass : $iconInactiveClass }}"/>
                            Date (récent → ancien)
                        </x-menu.item>

                        <x-menu.divider />

                        <!-- Filtres d'échéance -->
                        <x-menu.item wire:click="applyFilter('payment_due_date_asc')" class="{{ $activeFilter === 'payment_due_date_asc' ? $activeState : $inactiveState }} group">
                            <x-svg.clock class="w-4 h-4 transition-colors duration-200 {{ $activeFilter === 'payment_due_date_asc' ? $iconActiveClass : $iconInactiveClass }}"/>
                            Échéance (proche → loin)
                        </x-menu.item>

                        <x-menu.item wire:click="applyFilter('payment_due_date_desc')" class="{{ $activeFilter === 'payment_due_date_desc' ? $activeState : $inactiveState }} group">
                            <x-svg.clock class="w-4 h-4 transition-colors duration-200 {{ $activeFilter === 'payment_due_date_desc' ? $iconActiveClass : $iconInactiveClass }}"/>
                            Échéance (loin → proche)
                        </x-menu.item>

                        <x-menu.divider />

                        <!-- Filtres de montant -->
                        <x-menu.item wire:click="applyFilter('amount_asc')" class="{{ $activeFilter === 'amount_asc' ? $activeState : $inactiveState }} group">
                            <x-svg.dollar class="w-4 h-4 transition-colors duration-200 {{ $activeFilter === 'amount_asc' ? $iconActiveClass : $iconInactiveClass }}"/>
                            Prix (- → +)
                        </x-menu.item>

                        <x-menu.item wire:click="applyFilter('amount_desc')" class="{{ $activeFilter === 'amount_desc' ? $activeState : $inactiveState }} group">
                            <x-svg.dollar class="w-4 h-4 transition-colors duration-200 {{ $activeFilter === 'amount_desc' ? $iconActiveClass : $iconInactiveClass }}"/>
                            Prix (+ → -)
                        </x-menu.item>

                        <x-menu.divider />

                        <!-- Filtres alphabétiques -->
                        <x-menu.item wire:click="applyFilter('name_asc')" class="{{ $activeFilter === 'name_asc' ? $activeState : $inactiveState }} group">
                            <x-svg.atoz class="w-4 h-4 transition-colors duration-200 {{ $activeFilter === 'name_asc' ? $iconActiveClass : $iconInactiveClass }}"/>
                            A → Z
                        </x-menu.item>

                        <x-menu.item wire:click="applyFilter('name_desc')" class="{{ $activeFilter === 'name_desc' ? $activeState : $inactiveState }} group">
                            <x-svg.atoz class="w-4 h-4 rotate-180 transition-colors duration-200 {{ $activeFilter === 'name_desc' ? $iconActiveClass : $iconInactiveClass }}"/>
                            Z → A
                        </x-menu.item>

                        @if($activeFilter)
                            <x-menu.divider />

                            <div>
                                <x-menu.item wire:click="resetSort" class="flex items-center text-sm-medium text-slate-800 hover:bg-slate-100 transition-colors rounded">
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

                        <x-menu.divider />

                        <x-menu.item wire:click="toggleColumn('name')">
                            <x-form.checkbox-input
                                name="column_name"
                                model="visibleColumns.name"
                                label="Nom du fichier"
                                :checked="isset($visibleColumns['name']) && $visibleColumns['name']"
                                wire:click="toggleColumn('name')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('issued_date')">
                            <x-form.checkbox-input
                                name="column_issued_date"
                                model="visibleColumns.issued_date"
                                label="Date d'émission"
                                :checked="isset($visibleColumns['issued_date']) && $visibleColumns['issued_date']"
                                wire:click="toggleColumn('issued_date')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('payment_due_date')">
                            <x-form.checkbox-input
                                name="column_payment_due_date"
                                model="visibleColumns.payment_due_date"
                                label="Date d'échéance"
                                :checked="isset($visibleColumns['payment_due_date']) && $visibleColumns['payment_due_date']"
                                wire:click="toggleColumn('payment_due_date')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('amount')">
                            <x-form.checkbox-input
                                name="column_amount"
                                model="visibleColumns.amount"
                                label="Montant"
                                :checked="isset($visibleColumns['amount']) && $visibleColumns['amount']"
                                wire:click="toggleColumn('amount')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('payment_status')">
                            <x-form.checkbox-input
                                name="column_payment_status"
                                model="visibleColumns.payment_status"
                                label="Statut de paiement"
                                :checked="isset($visibleColumns['payment_status']) && $visibleColumns['payment_status']"
                                wire:click="toggleColumn('payment_status')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('issuer_name')">
                            <x-form.checkbox-input
                                name="column_issuer_name"
                                model="visibleColumns.issuer_name"
                                label="Émetteur"
                                :checked="isset($visibleColumns['issuer_name']) && $visibleColumns['issuer_name']"
                                wire:click="toggleColumn('issuer_name')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('type')">
                            <x-form.checkbox-input
                                name="column_type"
                                model="visibleColumns.type"
                                label="Type"
                                :checked="isset($visibleColumns['type']) && $visibleColumns['type']"
                                wire:click="toggleColumn('type')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('category')">
                            <x-form.checkbox-input
                                name="column_category"
                                model="visibleColumns.category"
                                label="Catégorie"
                                :checked="isset($visibleColumns['category']) && $visibleColumns['category']"
                                wire:click="toggleColumn('category')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('tags')">
                            <x-form.checkbox-input
                                name="column_tags"
                                model="visibleColumns.tags"
                                label="Tags"
                                :checked="isset($visibleColumns['tags']) && $visibleColumns['tags']"
                                wire:click="toggleColumn('tags')"
                            />
                        </x-menu.item>

                        <x-menu.divider />

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

        @if($invoices->isEmpty())
            <div class="p-6 text-center">
                <p class="text-gray-500">{{ __('Aucun fichier enregistré pour le moment.') }}</p>
            </div>
        @else
            <div class="w-full overflow-x-auto">
                <table class="w-full" aria-labelledby="tableTitle">
                    <thead>
                    <tr>
                        {{-- Colonne "Nom du fichier" (toujours visible) --}}
                        @if($visibleColumns['name'])
                            <th scope="col">
                                <button wire:click="sortBy('name')" class="flex items-center">
                                    <span>Nom du fichier</span>
                                    @if ($sortField === 'name')
                                        <svg class="ml-2 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             xmlns="http://www.w3.org/2000/svg">
                                            @if ($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                        @endif

                        {{-- Type --}}
                        @if($visibleColumns['type'])
                            <th scope="col" class="min-w-[150px]">
                                <button wire:click="sortBy('type')" class="flex items-center">
                                    <span>Type</span>
                                    @if ($sortField === 'type')
                                        <svg class="ml-2 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             xmlns="http://www.w3.org/2000/svg">
                                            @if ($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                        @endif

                        {{-- Catégorie --}}
                        @if($visibleColumns['category'])
                            <th scope="col" class="min-w-[150px]">
                                <button wire:click="sortBy('category')" class="flex items-center">
                                    <span>Catégorie</span>
                                    @if ($sortField === 'category')
                                        <svg class="ml-2 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             xmlns="http://www.w3.org/2000/svg">
                                            @if ($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                        @endif

                        {{-- Émetteur --}}
                        @if($visibleColumns['issuer_name'])
                        <th scope="col" class="min-w-[150px]">
                                <button wire:click="sortBy('issuer_name')" class="flex items-center">
                                    <span>Émetteur</span>
                                    @if ($sortField === 'issuer_name')
                                        <svg class="ml-2 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             xmlns="http://www.w3.org/2000/svg">
                                            @if ($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                        @endif

                        {{-- Montant --}}
                        @if($visibleColumns['amount'])
                            <th scope="col" class="min-w-[150px]">
                                <button wire:click="sortBy('amount')" class="flex items-center">
                                    <span>Montant</span>
                                    @if ($sortField === 'amount')
                                        <svg class="ml-2 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             xmlns="http://www.w3.org/2000/svg">
                                            @if ($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                        @endif

                        {{-- Statut de paiement --}}
                        @if($visibleColumns['payment_status'])
                            <th scope="col" class="min-w-[150px]">
                                <button wire:click="sortBy('payment_status')" class="flex items-center">
                                    <span>Statut</span>
                                    @if ($sortField === 'payment_status')
                                        <svg class="ml-2 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             xmlns="http://www.w3.org/2000/svg">
                                            @if ($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                        @endif

                        {{-- Date d'émission --}}
                        @if($visibleColumns['issued_date'])
                            <th scope="col" class="min-w-[150px]">
                                <button wire:click="sortBy('issued_date')" class="flex items-center">
                                    <span>Date d'émission</span>
                                    @if ($sortField === 'issued_date')
                                        <svg class="ml-2 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             xmlns="http://www.w3.org/2000/svg">
                                            @if ($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                        @endif

                        {{-- Date d'échéance --}}
                        @if($visibleColumns['payment_due_date'])
                            <th scope="col" class="min-w-[150px]">
                                <button wire:click="sortBy('payment_due_date')" class="flex items-center">
                                    <span>Date d'échéance</span>
                                    @if ($sortField === 'payment_due_date')
                                        <svg class="ml-2 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             xmlns="http://www.w3.org/2000/svg">
                                            @if ($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                        @endif

                        {{-- Tags --}}
                        @if($visibleColumns['tags'])
                            <th scope="col" class="min-w-[200px]">
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
                        <tr wire:loading.class="opacity-50">
                            {{-- Nom du fichier --}}
                            @if($visibleColumns['name'])
                                <td>
                                    <div class="flex items-center">
                                        @php
                                            $extension = $invoice->file->file_extension ?? null;
                                        @endphp

                                        <div class="mr-3 p-2 rounded">
                                            @if(View::exists('components.svg.file.' . $extension))
                                                <x-dynamic-component :component="'svg.file.' . $extension" class="w-6 h-6" />
                                            @else
                                                <x-svg.file.default class="w-6 h-6" />
                                            @endif
                                        </div>

                                        <div class="flex flex-col">
                                            <span class="text-sm-medium text-gray-900 dark:text-gray-400">{{ ucfirst($invoice->name) }}</span>
                                        </div>
                                    </div>
                                </td>
                            @endif

                            {{-- Type --}}
                            @if($visibleColumns['type'])
                                <td>
                                    {{ $invoice->type ?? 'Non spécifié' }}
                                </td>
                            @endif

                            {{-- Catégorie --}}
                            @if($visibleColumns['category'])
                                <td>
                                    {{ $invoice->category ?? 'Non spécifiée' }}
                                </td>
                            @endif

                            {{-- Émetteur --}}
                            @if($visibleColumns['issuer_name'])
                                <td>
                                    {{ $invoice->issuer_name ?? 'Non spécifié' }}
                                </td>
                            @endif

                            {{-- Montant --}}
                            @if($visibleColumns['amount'])
                                <td>
                                    {{ number_format($invoice->amount, 2, ',', ' ') }} €
                                </td>
                            @endif

                            {{-- Statut de paiement --}}
                            @if($visibleColumns['payment_status'])
                                <td>
                                    @php
                                        $statusClass = match($invoice->payment_status) {
                                            'paid' => 'bg-green-100 text-green-800',
                                            'partially_paid' => 'bg-yellow-100 text-yellow-800',
                                            'late' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };

                                        $statusText = match($invoice->payment_status) {
                                            'paid' => 'Payé',
                                            'partially_paid' => 'Partiellement payé',
                                            'late' => 'En retard',
                                            'unpaid' => 'Non payé',
                                            default => 'Non spécifié',
                                        };
                                    @endphp
                                    <span class="px-3 py-1 {{ $statusClass }} rounded-full text-xs-medium">
                                    {{ $statusText }}
                                </span>
                                </td>
                            @endif

                            {{-- Date d'émission --}}
                            @if($visibleColumns['issued_date'])
                                <td>
                                    @if($invoice->issued_date)
                                        {{ \Carbon\Carbon::parse($invoice->issued_date)->format('j F Y') }}
                                    @else
                                        {{ __('Non spécifiée') }}
                                    @endif
                                </td>
                            @endif

                            {{-- Date d'échéance --}}
                            @if($visibleColumns['payment_due_date'])
                                <td>
                                    @if($invoice->payment_due_date)
                                        {{ \Carbon\Carbon::parse($invoice->payment_due_date)->format('j F Y') }}
                                    @else
                                        {{ __('Non spécifiée') }}
                                    @endif
                                </td>
                            @endif

                            {{-- Tags --}}
                            @if($visibleColumns['tags'])
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
                                    <x-menu>
                                        <x-menu.button class="rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 p-1">
                                            <x-svg.dots class="w-5 h-5 text-gray-500"/>
                                        </x-menu.button>
                                        <x-menu.items>
                                            <x-menu.item wire:click="showInvoiceModal({{ $invoice->id }})">
                                                <x-svg.show class="w-4 h-4 group-hover:text-gray-900"/>
                                                {{ __('Voir l’aperçu') }}
                                            </x-menu.item>
                                            <x-menu.item wire:click="showInvoicePage({{ $invoice->id }})">
                                                <x-svg.help class="w-4 h-4 group-hover:text-gray-900"/>
                                                {{ __('Voir en détail') }}
                                            </x-menu.item>
                                            <x-menu.divider />
                                            @if(!$invoice->is_favorite)
                                                <x-menu.item wire:click="toggleFavorite({{ $invoice->id }})">
                                                    <x-svg.star class="w-4 h-4 group-hover:text-gray-900"/>
                                                    {{ __('Mettre en favori') }}
                                                </x-menu.item>
                                            @endif
                                            <x-menu.item wire:click="downloadInvoice({{ $invoice->id }})">
                                                <x-svg.download class="w-4 h-4 group-hover:text-gray-900"/>
                                                {{ __('Télécharger') }}
                                            </x-menu.item>
                                            <x-menu.item wire:click="showEditPage({{ $invoice->id }})">
                                                <x-svg.edit class="w-4 h-4" class="group-hover:text-gray-900"/>
                                                {{ __('Modifier') }}
                                            </x-menu.item>
                                            <x-menu.item wire:click="showArchiveForm({{ $invoice->id }})" class="group hover:text-red-500">
                                                <x-svg.trash class="w-4 h-4 group-hover:text-red-500"/>
                                                {{ __('Archiver') }}
                                            </x-menu.item>
                                        </x-menu.items>
                                    </x-menu>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($invoices->hasPages())
                    <div class="p-4">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>
        @endif
    </section>

    <!-- Modal pour afficher les factures d'un dossier spécifique avec design de cartes -->
    @if($showFolderModal)
        <x-modal wire:model="showFolderModal">
            <x-modal.panel class="max-w-5xl bg-white dark:bg-gray-900">
                <!-- En-tête avec titre et compteur -->
                <div class="sticky top-0 px-6 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 z-20">
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $folderTitle }}</h2>
                        <span class="inline-flex px-2.5 py-1 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-full">
                            {{ count($folderInvoices) }} factures
                        </span>
                    </div>
                </div>

                <!-- Contenu principal -->
                <div class="p-6 bg-gray-50 dark:bg-gray-950 overflow-y-auto max-h-[70vh]">
                    @if($folderInvoices->isEmpty())
                        <!-- État vide -->
                        <div class="flex flex-col items-center justify-center py-16 rounded-lg bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
                            <x-heroicon-o-document-text class="w-14 h-14 text-gray-300 dark:text-gray-700 mb-3"/>
                            <p class="text-base font-medium text-gray-700 dark:text-gray-300">{{ __('Aucune facture dans cette catégorie') }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">{{ __('Les factures que vous ajouterez apparaîtront ici !') }}</p>
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
                                                    <x-dynamic-component :component="'svg.file.' . $extension" class="w-5 h-5" />
                                                @else
                                                    <x-svg.file.default class="w-5 h-5" />
                                                @endif
                                            </div>
                                            <h3 class="font-medium text-gray-900 dark:text-white truncate">
                                                {{ $invoice->name }}
                                            </h3>
                                        </div>

                                        <x-menu>
                                            <x-menu.button class="rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 py-0.5 px-1">
                                                <x-svg.dots class="w-5 h-5 text-gray-500"/>
                                            </x-menu.button>
                                            <x-menu.items>
                                                <x-menu.item wire:click="toggleFavorite({{ $invoice->id }})">
                                                    <x-svg.star class="w-4 h-4 group-hover:text-gray-900"/>
                                                    @if($invoice->is_favorite)
                                                        {{ __('Retirer des favoris') }}
                                                    @else
                                                        {{ __('Mettre en favori') }}
                                                    @endif
                                                </x-menu.item>
                                                <x-menu.item wire:click="showEditPage({{ $invoice->id }})">
                                                    <x-svg.edit class="w-4 h-4 group-hover:text-gray-900"/>
                                                    {{ __('Modifier') }}
                                                </x-menu.item>
                                                <x-menu.item wire:click="showArchiveForm({{ $invoice->id }})" class="group hover:text-red-600">
                                                    <x-svg.trash class="w-4 h-4 group-hover:text-red-600"/>
                                                    {{ __('Archiver') }}
                                                </x-menu.item>
                                            </x-menu.items>
                                        </x-menu>
                                    </div>

                                    <!-- Corps de la carte -->
                                    <div class="p-4 border-t border-b border-slate-100">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="space-y-1">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Date d\'émission') }}</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $invoice->issued_date ? \Carbon\Carbon::parse($invoice->issued_date)->format('j M Y') : 'Non spécifiée' }}
                                                </p>
                                            </div>
                                            <div class="space-y-1">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Échéance') }}</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $invoice->payment_due_date ? \Carbon\Carbon::parse($invoice->payment_due_date)->format('j M Y') : 'Non spécifiée' }}
                                                </p>
                                            </div>
                                            <div class="space-y-1">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Émetteur') }}</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate" title="{{ $invoice->issuer_name ?? 'Non spécifié' }}">
                                                    {{ $invoice->issuer_name ?? 'Non spécifié' }}
                                                </p>
                                            </div>
                                            <div class="space-y-1">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Montant') }}</p>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ number_format($invoice->amount, 2, ',', ' ') }} €
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pied de la carte -->
                                    <div class="dark:border-gray-800 px-4 py-3 flex justify-between items-center">
                                        @php
                                            $statusClass = match($invoice->payment_status) {
                                                'paid' => 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 border-green-100 dark:border-green-800',
                                                'partially_paid' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border-amber-100 dark:border-amber-800',
                                                'late' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400 border-red-100 dark:border-red-800',
                                                default => 'bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-400 border-gray-100 dark:border-gray-700',
                                            };

                                            $statusText = match($invoice->payment_status) {
                                                'paid' => 'Payé',
                                                'partially_paid' => 'Partiellement payé',
                                                'late' => 'En retard',
                                                'unpaid' => 'Non payé',
                                                default => 'Non spécifié',
                                            };
                                        @endphp
                                        <span class="px-2.5 py-1 {{ $statusClass }} rounded-full text-xs font-medium border">
                                            {{ $statusText }}
                                        </span>

                                        <div class="flex gap-1">
                                            <button wire:click="showInvoiceModal({{ $invoice->id }})"
                                                    class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                                    title="Voir">
                                                <x-svg.show class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
                                            </button>
                                            <button wire:click="downloadInvoice({{ $invoice->id }})"
                                                    class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                                    title="Télécharger">
                                                <x-svg.download class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Pied de modal -->
                <x-modal.footer class="bg-white dark:bg-gray-900 border-t border-slate-200 dark:border-gray-800 px-6 py-4">
                    <div class="flex items-center justify-between w-full">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ count($folderInvoices) }} {{ __('factures trouvées') }}
                        </div>
                        <x-modal.close>
                            <button type="button" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium transition-colors">
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
                <p class="sticky top-0 p-5 px-8 max-w-full text-xl font-bold bg-white dark:bg-gray-800 dark:border-gray-700 z-20">
                    {{ __('Aperçu de la facture') }}
                </p>

                <div class="p-1 border-b border-gray-200 bg-gray-50 dark:bg-gray-900 relative overflow-auto">

                    <!-- Loader -->
                    <div wire:loading class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-gray-800/80 z-10">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                    </div>

                    @php
                        $isImage = $filePath && preg_match('/\.(jpg|jpeg|png|gif)$/i', $filePath);
                        $isPdf = $filePath && preg_match('/\.pdf$/i', $filePath);
                    @endphp

                    @if($isImage)
                        <div class="flex-center p-1">
                            <img src="{{ $filePath }}" alt="Aperçu de la facture" class="min-h-120 rounded-xl flex-center">
                        </div>
                    @elseif($isPdf)
                        <div class="flex-center p-1">
                            <embed src="{{ $filePath }}" type="application/pdf" width="100%" height="100%" class="min-h-120 rounded-xl" />
                        </div>
                    @else
                        <div class="flex-center flex-col p-8">
                            <x-svg.file-size class="w-24 h-24 text-gray-400 mb-6"/>
                            <p class="text-xl font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Prévisualisation non disponible
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center max-w-md mb-8">
                                Le type de fichier "{{ strtoupper($fileExtension) }}" ne peut pas être prévisualisé
                                directement. Veuillez télécharger le fichier pour le consulter.
                            </p>
                            <a href="{{ $filePath }}" download class="button-primary">
                                <x-svg.download class="mr-2"/>
                                Télécharger le fichier
                            </a>
                        </div>
                    @endif
                </div>

                <x-modal.footer class="bg-white dark:bg-gray-800 border-t border-gray-400 dark:border-gray-700">
                    <div class="flex justify-between w-full">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-medium">Type:</span>
                            {{ strtoupper($fileExtension) }}
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

    <!-- Modale pour archiver d'une facture -->
    @if($showArchiveFormModal)
        <x-modal wire:model="showArchiveFormModal">
            <x-modal.panel>
                <form wire:submit.prevent="archiveInvoice">
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
                                    <label for="confirmation" class="text-sm-medium text-gray-800">
                                        {{ __('Veuillez tapper "CONFIRMER" pour confirmer la suppression.') }}
                                    </label>
                                    <input x-model="confirmation" placeholder="CONFIRMER" type="text" id="confirmation"
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
                                    {{ __('Archiver') }}
                                </button>
                            </x-modal.close>
                        </x-modal.footer>
                    </div>
                </form>
            </x-modal.panel>
        </x-modal>
    @endif

    @if($archivedWithSuccess)
        <x-flash-message
            icon="archive"
            title="Facture archivée !"
            method="$set('archivedWithSuccess', false)"
        />
    @endif

    @if($downloadNotWorking)
        <x-flash-message
            icon="import"
            title="Le téléchargement n'a pas fonctionné !"
            method="$set('downloadNotWorking', false)"
        />
    @endif
</div>
