<div>
    {{-- En-tête du tableau --}}
    <div class="w-full overflow-hidden bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 border-b border-gray-200 dark:border-gray-700 dark:bg-gray-800">
            <h1 class="text-md-semibold mb-3 sm:mb-0 dark:text-white">Tous les fichiers</h1>
            <div class="flex flex-wrap gap-2">
                {{-- Filtres --}}
                <x-menu>
                    <x-menu.button class="button-primary flex items-center">
                        <x-svg.filter/>
                        {{ $activeFilter ? $availableFilters[$activeFilter] : 'Filtres' }}
                    </x-menu.button>

                    <x-menu.items class="origin-top-left mt-2 py-3 px-4 max-w-[20rem] w-auto">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-400">Filtres</p>
                            @if($activeFilter)
                                <button wire:click="resetSort"
                                        class="text-blue-600 text-sm hover:text-blue-800 flex items-center">
                                    <x-svg.reset class="mr-1.5 text-blue-600 text-sm hover:text-blue-800" />
                                    Réinitialiser
                                </button>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-3 mb-3">
                            @php
                                $activeState = 'bg-blue-100 text-blue-700 border border-blue-300 dark:bg-blue-200';
                                $inactiveState = 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-700';
                            @endphp

                            {{-- Statut --}}
                            <button wire:click="applyFilter('payment_status_paid')" class="button-rounded {{ $activeFilter === 'payment_status_paid' ? $activeState : $inactiveState }}">
                                <x-svg.validate class="w-3.5 h-3.5" />
                                Payé
                            </button>

                            <button wire:click="applyFilter('payment_status_unpaid')" class="button-rounded {{ $activeFilter === 'payment_status_unpaid' ? $activeState : $inactiveState }}">
                                <x-svg.validate class="w-3.5 h-3.5" />
                                Impayé
                            </button>

                            {{-- Filtres de date d'ajout --}}
                            <button wire:click="applyFilter('issued_date_asc')" class="button-rounded {{ $activeFilter === 'issued_date_asc' ? $activeState : $inactiveState }}">
                                <x-svg.calendar class="w-3.5 h-3.5" />
                                Date (ancien → récent)
                            </button>

                            <button wire:click="applyFilter('issued_date_desc')" class="button-rounded {{ $activeFilter === 'issued_date_desc' ? $activeState : $inactiveState }}">
                                <x-svg.calendar class="w-3.5 h-3.5" />
                                Date (récent → ancien)
                            </button>

                            {{-- Filtres d'échéance --}}
                            <button wire:click="applyFilter('payment_due_date_asc')" class="button-rounded {{ $activeFilter === 'payment_due_date_asc' ? $activeState : $inactiveState }}">
                                <x-svg.clock class="w-3.5 h-3.5" />
                                Échéance (proche → loin)
                            </button>

                            <button wire:click="applyFilter('payment_due_date_desc')" class="button-rounded {{ $activeFilter === 'payment_due_date_desc' ? $activeState : $inactiveState }}">
                                <x-svg.clock class="w-3.5 h-3.5" />
                                Échéance (loin → proche)
                            </button>

                            {{-- Filtres de montant --}}
                            <button wire:click="applyFilter('amount_asc')" class="button-rounded {{ $activeFilter === 'amount_asc' ? $activeState : $inactiveState }}">
                                <x-svg.dollar class="w-3.5 h-3.5" />
                                Prix (- → +)
                            </button>

                            <button wire:click="applyFilter('amount_desc')" class="button-rounded {{ $activeFilter === 'amount_desc' ? $activeState : $inactiveState }}">
                                <x-svg.dollar class="w-3.5 h-3.5" />
                                Prix (+ → -)
                            </button>

                            {{-- Filtres de taille --}}
                            <button wire:click="applyFilter('file_size_asc')" class="button-rounded {{ $activeFilter === 'file_size_asc' ? $activeState : $inactiveState }}">
                                <x-svg.file-size class="w-3.5 h-3.5" />
                                Taille (petit → grand)
                            </button>

                            <button wire:click="applyFilter('file_size_desc')" class="button-rounded {{ $activeFilter === 'file_size_desc' ? $activeState : $inactiveState }}">
                                <x-svg.file-size class="w-3.5 h-3.5" />
                                Taille (grand → petit)
                            </button>

                            {{-- Filtres alphabétiques --}}
                            <button wire:click="applyFilter('name_asc')" class="button-rounded {{ $activeFilter === 'name_asc' ? $activeState : $inactiveState }}">
                                <x-svg-atoz class="w-3.5 h-3.5" />
                                A → Z
                            </button>

                            <button wire:click="applyFilter('name_desc')" class="button-rounded {{ $activeFilter === 'name_desc' ? $activeState : $inactiveState }}">
                                <x-svg-atoz class="w-3.5 h-3.5 rotate-180" />
                                Z → A
                            </button>
                        </div>
                    </x-menu.items>
                </x-menu>

                {{-- Colonnes --}}
                <x-menu>
                    <x-menu.button class="button-primary">
                        <x-svg.columns />
                        Colonnes
                    </x-menu.button>

                    <x-menu.items class="mt-2 w-56">
                        <p class="px-4 py-3 text-sm-medium text-gray-700 dark:text-gray-400">
                            Colonnes à afficher
                        </p>

                        <x-divider />

                        <x-menu.item wire:click="toggleColumn('name')" >
                            <x-form.checkbox-input
                                name="column_name"
                                model="visibleColumns.name"
                                label="Nom du fichier"
                                :checked="isset($visibleColumns['name']) && $visibleColumns['name']"
                                wire:click="toggleColumn('name')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('file_size')" >
                            <x-form.checkbox-input
                                name="column_file_size"
                                model="visibleColumns.file_size"
                                label="Taille du fichier"
                                :checked="isset($visibleColumns['file_size']) && $visibleColumns['file_size']"
                                wire:click="toggleColumn('file_size')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('issued_date')" >
                            <x-form.checkbox-input
                                name="column_issued_date"
                                model="visibleColumns.issued_date"
                                label="Date d'ajout"
                                :checked="isset($visibleColumns['issued_date']) && $visibleColumns['issued_date']"
                                wire:click="toggleColumn('issued_date')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('payment_due_date')" >
                            <x-form.checkbox-input
                                name="column_payment_due_date"
                                model="visibleColumns.payment_due_date"
                                label="Date d'échéance"
                                :checked="isset($visibleColumns['payment_due_date']) && $visibleColumns['payment_due_date']"
                                wire:click="toggleColumn('payment_due_date')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('amount')" >
                            <x-form.checkbox-input
                                name="column_amount"
                                model="visibleColumns.amount"
                                label="Montant"
                                :checked="isset($visibleColumns['amount']) && $visibleColumns['amount']"
                                wire:click="toggleColumn('amount')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('payment_status')" >
                            <x-form.checkbox-input
                                name="column_payment_status"
                                model="visibleColumns.payment_status"
                                label="Statut de paiement"
                                :checked="isset($visibleColumns['payment_status']) && $visibleColumns['payment_status']"
                                wire:click="toggleColumn('payment_status')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('issuer_name')" >
                            <x-form.checkbox-input
                                name="column_issuer_name"
                                model="visibleColumns.issuer_name"
                                label="Émetteur"
                                :checked="isset($visibleColumns['issuer_name']) && $visibleColumns['issuer_name']"
                                wire:click="toggleColumn('issuer_name')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('type')" >
                            <x-form.checkbox-input
                                name="column_type"
                                model="visibleColumns.type"
                                label="Type"
                                :checked="isset($visibleColumns['type']) && $visibleColumns['type']"
                                wire:click="toggleColumn('type')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('category')" >
                            <x-form.checkbox-input
                                name="column_category"
                                model="visibleColumns.category"
                                label="Catégorie"
                                :checked="isset($visibleColumns['category']) && $visibleColumns['category']"
                                wire:click="toggleColumn('category')"
                            />
                        </x-menu.item>

                        <x-menu.item wire:click="toggleColumn('tags')" >
                            <x-form.checkbox-input
                                name="column_tags"
                                model="visibleColumns.tags"
                                label="Tags"
                                :checked="isset($visibleColumns['tags']) && $visibleColumns['tags']"
                                wire:click="toggleColumn('tags')"
                            />
                        </x-menu.item>

                        <x-divider />

                        <div class="p-1.5">
                            <x-menu.item wire:click="resetColumns" class="p-2 flex items-center text-sm-medium text-slate-800 hover:bg-slate-100 transition-colors rounded">
                                <x-svg.reset />
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

                        {{-- Colonne "Taille du fichier" --}}
                        @if($visibleColumns['file_size'])
                            <th scope="col" class="min-w-[150px]">
                                <button wire:click="sortBy('file_size')" class="flex items-center">
                                    <span>Taille du fichier</span>
                                    @if ($sortField === 'file_size')
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

                        {{-- Colonnes supplémentaires --}}
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

                        {{-- Date d'ajout --}}
                        @if($visibleColumns['issued_date'])
                            <th scope="col" class="min-w-[150px]">
                                <button wire:click="sortBy('issued_date')" class="flex items-center">
                                    <span>Date d'ajout</span>
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
                                            $extension = pathinfo($invoice->file_path, PATHINFO_EXTENSION);
                                            $badgeClass = match(strtolower($extension)) {
                                                'pdf' => 'bg-red-100 text-red-600',
                                                'docx', 'doc' => 'bg-blue-100 text-blue-600',
                                                'xlsx', 'xls' => 'bg-green-100 text-green-600',
                                                'jpg', 'jpeg', 'png' => 'bg-yellow-100 text-yellow-600',
                                                default => 'bg-gray-100 text-gray-600'
                                            };
                                        @endphp

                                        <div class="{{ $badgeClass }} mr-3 p-2 rounded">
                                            <span class="text-xs-medium">{{ strtoupper($extension) }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm-medium text-gray-900 dark:text-gray-400">{{ ucfirst($invoice->name) }}</span>
                                        </div>
                                    </div>
                                </td>
                            @endif

                            {{-- Taille du fichier --}}
                            @if($visibleColumns['file_size'])
                                <td>
                                    {{ $invoice->formatted_file_size ?? 'N/A' }}
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

                            {{-- Date d'ajout --}}
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
                                    <div class="flex flex-wrap gap-1">
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
                                        <x-menu.items class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-sm bg-white z-20">
                                            <x-menu.item wire:click="showFile({{ $invoice->id }})">
                                                <x-svg.show class="w-4 h-4" />
                                                {{ __('Voir la facture') }}
                                            </x-menu.item>
                                            <x-menu.item wire:click="downloadInvoice({{ $invoice->id }})">
                                                <x-svg.download class="w-4 h-4" />
                                                {{ __('Télécharger') }}
                                            </x-menu.item>
                                            <x-menu.item wire:click="showEditForm({{ $invoice->id }})">
                                                <x-svg.edit class="w-4 h-4" />
                                                {{ __('Modifier') }}
                                            </x-menu.item>
                                            <x-menu.item wire:click="showDeleteForm({{ $invoice->id }})" class="hover:text-red-600">
                                                <x-svg.trash class="w-4 h-4 text-red-500"/>
                                                {{ __('Supprimer') }}
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
    </div>

    <!-- Modale qui affiche la facture -->
    <x-modal wire:model="showFileModal">
        <x-modal.panel>
            <div class="sticky top-0 p-5 px-8 max-w-full text-xl font-bold border-b border-[#dde2e9] bg-white z-20">
                {{ __('Aperçu du fichier') }}
            </div>
            <iframe src="{{ $fileUrl }}" width="100%" height="600px"></iframe>
            <x-modal.footer>
                <x-modal.close>
                    <button type="button">
                        {{ __('Fermer') }}
                    </button>
                </x-modal.close>
            </x-modal.footer>
        </x-modal.panel>
    </x-modal>

    <!-- Modale de modification d'une facture -->
    <x-modal wire:model="showEditFormModal">
        <x-modal.panel>
            <div class="sticky top-0 p-5 px-8 max-w-full text-xl font-bold border-b border-[#dde2e9] bg-white z-10">
                {{ __('Modifier votre facture juste ici 👇') }}
            </div>

            <form wire:submit.prevent="updateInvoice">
                @csrf

                <div class="flex flex-col gap-4 p-8">
                    Formulaire d'édition de la facture
                </div>

                <x-modal.footer>
                    <x-modal.close>
                        <button type="button" class="cancel">
                            {{ __('Annuler') }}
                        </button>
                    </x-modal.close>

                    <button type="submit" class="save">
                        {{ __('Mettre à jour') }}
                    </button>
                </x-modal.footer>
            </form>
        </x-modal.panel>
    </x-modal>

    <!-- Modale de suppression d'une facture -->
    <x-modal wire:model="showDeleteFormModal">
        <x-modal.panel>
            <form wire:submit.prevent="deleteInvoice">
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
                                <strong class="font-semibold"> {{ $invoice->name }}</strong>&nbsp;
                                {{ __('? Toutes les données seront supprimées. Cette action est irréversible.') }}
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
                                {{ __('Supprimer') }}
                            </button>
                        </x-modal.close>
                    </x-modal.footer>
                </div>
            </form>
        </x-modal.panel>
    </x-modal>

    @if($addedWithSuccess)
        <x-flash-message
            icon="add"
            title="Facture ajoutée avec succès !"
            method="$set('addedWithSuccess', false)"
        />
    @endif

    @if($editWithSuccess)
        <x-flash-message
            icon="edit"
            title="Facture modifiée avec succès !"
            method="$set('editWithSuccess', false)"
        />
    @endif

    @if($deleteWithSuccess)
        <x-flash-message
            icon="delete"
            title="Facture supprimée avec succès !"
            method="$set('deleteWithSuccess', false)"
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
