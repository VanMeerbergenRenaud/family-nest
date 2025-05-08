<div>
    <section class="relative w-full overflow-hidden bg-white dark:bg-gray-800 rounded-2xl border border-slate-200">

        {{-- En-tête --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 p-4 pl-6 border-b border-gray-200 dark:border-gray-700 dark:bg-gray-800">
            <h3 role="heading" aria-level="3" class="flex flex-wrap gap-3 text-md-semibold mb-3 sm:mb-0 dark:text-white">
                Tous les fichiers
                <span aria-hidden="true" class="relative -top-0.5 px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs-medium dark:bg-gray-700 dark:text-gray-200">
                    {{ \Illuminate\Support\Number::format($invoices->total()) }}
                    {{ $invoices->total() <= 1 ? 'facture' : 'factures' }}
               </span>
            </h3>

            <div class="flex flex-wrap items-center md:justify-end gap-2">
                <x-dashboard.search />

                {{-- Filtres --}}
                <x-menu>
                    <x-menu.button class="button-primary flex items-center">
                        <x-svg.filter/>
                        {{ $activeFilter ? $availableFilters[$activeFilter] : 'Filtres' }}
                    </x-menu.button>

                    <x-menu.items class="mt-2 w-68">
                        <p class="px-2.5 py-2 text-sm-medium text-gray-700 dark:text-gray-400">Filtres</p>

                        <x-menu.divider/>

                        @php
                            $activeState = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-white';
                            $inactiveState = 'bg-white hover:bg-gray-100 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-700';
                            $iconActiveClass = 'text-gray-900 dark:text-white';
                            $iconInactiveClass = 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-200';
                        @endphp

                        <!-- Filtres alphabétiques -->
                        <x-menu.item wire:click="applyFilter('name_asc')" class="justify-between group {{ $activeFilter === 'name_asc' ? $activeState : $inactiveState }}">
                            <span class="flex items-center gap-x-3 text-sm-medium">
                                <x-svg.alphabetic class="{{ $activeFilter === 'name_asc' ? $iconActiveClass : $iconInactiveClass }} transition-colors duration-200" />
                                Alphabétique (a → z)
                            </span>
                            <x-svg.atoz />
                        </x-menu.item>

                        <x-menu.item wire:click="applyFilter('name_desc')" class="justify-between group {{ $activeFilter === 'name_desc' ? $activeState : $inactiveState }}">
                            <span class="flex items-center gap-x-3 text-sm-medium">
                                <x-svg.alphabetic class="duration-200 {{ $activeFilter === 'name_desc' ? $iconActiveClass : $iconInactiveClass }} rotate-180 transition-colors" />
                                Alphabétique (z → a)
                            </span>
                            <x-svg.atoz class="rotate-180" />
                        </x-menu.item>

                        <x-menu.divider/>

                        <!-- Filtres de montant -->
                        <x-menu.item wire:click="applyFilter('amount_asc')" class="justify-between group {{ $activeFilter === 'amount_asc' ? $activeState : $inactiveState }}">
                            <span class="flex items-center gap-x-3 text-sm-medium">
                                <x-svg.dollar class="{{ $activeFilter === 'amount_asc' ? $iconActiveClass : $iconInactiveClass }} transition-colors duration-200" />
                                Prix (croissant)
                            </span>
                            <x-svg.atoz />
                        </x-menu.item>

                        <x-menu.item wire:click="applyFilter('amount_desc')" class="justify-between group {{ $activeFilter === 'amount_desc' ? $activeState : $inactiveState }}">
                            <span class="flex items-center gap-x-3 text-sm-medium">
                                <x-svg.dollar class="{{ $activeFilter === 'amount_asc' ? $iconActiveClass : $iconInactiveClass }} transition-colors duration-200" />
                                Prix (décroissant)
                            </span>
                            <x-svg.atoz class="rotate-180" />
                        </x-menu.item>

                        <x-menu.divider/>

                        <!-- Filtres d'échéance -->
                        <x-menu.item wire:click="applyFilter('payment_due_date_asc')" class="justify-between group {{ $activeFilter === 'payment_due_date_asc' ? $activeState : $inactiveState }}">
                            <span class="flex items-center gap-x-3 text-sm-medium">
                                <x-svg.clock class="{{ $activeFilter === 'payment_due_date_asc' ? $iconActiveClass : $iconInactiveClass }} transition-colors duration-200" />
                                Échéance (ancienne)
                            </span>
                            <x-svg.atoz />
                        </x-menu.item>

                        <x-menu.item wire:click="applyFilter('payment_due_date_desc')" class="justify-between group {{ $activeFilter === 'payment_due_date_desc' ? $activeState : $inactiveState }}">
                            <span class="flex items-center gap-x-3 text-sm-medium">
                                <x-svg.clock class="{{ $activeFilter === 'payment_due_date_desc' ? $iconActiveClass : $iconInactiveClass }} transition-colors duration-200" />
                                Échéance (récente)
                            </span>
                            <x-svg.atoz class="rotate-180" />
                        </x-menu.item>

                        <x-menu.divider/>

                        <!-- Filtres de date d'émission -->
                        <x-menu.item wire:click="applyFilter('issued_date_asc')" class="justify-between group {{ $activeFilter === 'issued_date_asc' ? $activeState : $inactiveState }}">
                            <span class="flex items-center gap-x-3 text-sm-medium">
                                <x-svg.calendar class="{{ $activeFilter === 'issued_date_asc' ? $iconActiveClass : $iconInactiveClass }} transition-colors duration-200" />
                                Émission (ancienne)
                            </span>
                            <x-svg.atoz />
                        </x-menu.item>

                        <x-menu.item wire:click="applyFilter('issued_date_desc')" class="justify-between group {{ $activeFilter === 'issued_date_desc' ? $activeState : $inactiveState }}">
                            <span class="flex items-center gap-x-3 text-sm-medium">
                                <x-svg.calendar class="{{ $activeFilter === 'issued_date_desc' ? $iconActiveClass : $iconInactiveClass }} transition-colors duration-200" />
                                Émission (récente)
                            </span>
                            <x-svg.atoz class="rotate-180" />
                        </x-menu.item>

                        @if($activeFilter)
                            <x-menu.divider/>

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

                        <x-menu.divider/>

                        @php
                            $columnLabels = [
                                'name' => 'Nom du fichier',
                                'reference' => 'Référence',
                                'type' => 'Type',
                                'category' => 'Catégorie',
                                'issuer_name' => 'Émetteur',
                                'amount' => 'Montant',
                                'payment_status' => 'Statut de paiement',
                                'issued_date' => 'Date d’émission',
                                'payment_due_date' => 'Date d’échéance',
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
                            <x-menu.item wire:click="resetColumns"
                                         class="flex items-center text-sm-medium text-slate-800 hover:bg-slate-100 transition-colors rounded">
                                <x-svg.reset/>
                                Réinitialiser
                            </x-menu.item>
                        </div>
                    </x-menu.items>
                </x-menu>
            </div>
        </div>

        {{-- Table --}}
        <div class="w-full overflow-x-auto scrollbar-hidden">
            <table class="w-full" aria-labelledby="tableTitle">
                <thead>
                    <tr>
                        {{-- Checkboxes... --}}
                        <td class="p-0 pl-2">
                            <x-dashboard.check-all />
                        </td>

                        @if($visibleColumns['name'] ?? false)
                            <th scope="col" class="max-sm:px-1 min-w-[12.5rem]">
                                <x-dashboard.sortable column="name" :$sortCol :$sortAsc>
                                    <span class="text-xs-semibold">Nom de la facture</span>
                                </x-dashboard.sortable>
                            </th>
                        @endif

                        @if($visibleColumns['reference'] ?? false)
                            <th scope="col" class="min-w-[10rem]">
                                <x-dashboard.sortable column="reference" :$sortCol :$sortAsc>
                                    <span class="text-xs-semibold">Référence</span>
                                </x-dashboard.sortable>
                            </th>
                        @endif

                        @if($visibleColumns['type'] ?? false)
                            <th scope="col" class="min-w-[10rem]">
                                <x-dashboard.sortable column="type" :$sortCol :$sortAsc>
                                    <span class="text-xs-semibold">Type</span>
                                </x-dashboard.sortable>
                            </th>
                        @endif

                        @if($visibleColumns['category'] ?? false)
                            <th scope="col" class="min-w-[10rem]">
                                <x-dashboard.sortable column="category" :$sortCol :$sortAsc>
                                    <span class="text-xs-semibold">Catégorie</span>
                                </x-dashboard.sortable>
                            </th>
                        @endif

                        @if($visibleColumns['issuer_name'] ?? false)
                            <th scope="col" class="min-w-[10rem]">
                                <x-dashboard.sortable column="issuer_name" :$sortCol :$sortAsc>
                                    <span class="text-xs-semibold">Émetteur</span>
                                </x-dashboard.sortable>
                            </th>
                        @endif

                        @if($visibleColumns['amount'] ?? false)
                            <th scope="col" class="min-w-[10rem]">
                                <x-dashboard.sortable column="amount" :$sortCol :$sortAsc>
                                    <span class="text-xs-semibold">Montant</span>
                                </x-dashboard.sortable>
                            </th>
                        @endif

                        @if($visibleColumns['payment_status'] ?? false)
                            <th scope="col" class="min-w-[10rem]">
                                <x-dashboard.sortable column="payment_status" :$sortCol :$sortAsc>
                                    <span class="text-xs-semibold">Statut</span>
                                </x-dashboard.sortable>
                            </th>
                        @endif

                        @if($visibleColumns['issued_date'] ?? false)
                            <th scope="col" class="min-w-[12.5rem]">
                                <x-dashboard.sortable column="issued_date" :$sortCol :$sortAsc>
                                    <span class="text-xs-semibold">Date d'émission</span>
                                </x-dashboard.sortable>
                            </th>
                        @endif

                        @if($visibleColumns['payment_due_date'] ?? false)
                            <th scope="col" class="min-w-[12.5rem]">
                                <x-dashboard.sortable column="payment_due_date" :$sortCol :$sortAsc>
                                    <span class="text-xs-semibold">Date d'échéance</span>
                                </x-dashboard.sortable>
                            </th>
                        @endif

                        @if($visibleColumns['tags'] ?? false)
                            <th scope="col" class="min-w-[12.5rem]">
                                <span class="text-xs-semibold">Tags associés</span>
                            </th>
                        @endif

                        <th scope="col" class="text-right text-xs-semibold">
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
                                    <input wire:model="selectedInvoiceIds"
                                           value="{{ $invoice->id }}"
                                           type="checkbox"
                                           class="peer h-4 w-4 cursor-pointer transition-all appearance-none rounded border border-slate-300 checked:bg-blue-700 checked:border-blue-700"
                                    >
                                    <span class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none">
                                    <x-svg.check class="text-white h-3 w-3"/>
                                </span>
                                </label>
                            </td>

                            {{-- Nom du fichier --}}
                            @if($visibleColumns['name'] ?? false)
                                <td class="max-sm:px-1">
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

                                        <div class="flex flex-col max-sm:max-w-[9rem] max-w-[12.5rem]">
                                           <span class="text-sm-medium text-gray-900 truncate block">{{ $invoice->name }}</span>
                                        </div>
                                    </div>
                                </td>
                            @endif

                            {{-- Référence --}}
                            @if($visibleColumns['reference'] ?? false)
                                <td>
                                    {{ $invoice->reference ?? 'Non spécifiée' }}
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
                                    <x-invoices.menu-actions :$invoice/>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if($invoices->count() <= 5)
                        <tr>
                            <td colspan="100%" class="h-56">
                                @if($invoices->count() < 5 && $invoices->count() > 0)
                                    <div class="h-[15rem]"></div>
                                @elseif($invoices->isEmpty())
                                    <div class="flex-center flex-col h-[20rem]">
                                        <x-svg.error class="text-gray-500 w-6 h-6" />
                                        <h3 role="heading" aria-level="3" class="mt-4 text-lg-semibold text-gray-900">
                                            Aucune facture trouvée
                                        </h3>
                                        <p class="text-center mt-2 text-sm text-gray-500">
                                            Vous n'avez pas encore de factures.<br>
                                            Veuillez commencer par ajouter une facture !
                                        </p>
                                        <a href="{{ route('invoices.create') }}" title="Vers la page de création d'une facture"
                                           class="mt-6 button-tertiary" wire:navigate>
                                            <x-svg.add class="mr-1 text-white"/>
                                            {{ __('Ajouter une facture') }}
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Bulk Actions -->
        <x-dashboard.bulk-actions :invoices="$invoices" />

        <!-- Pagination -->
        @if($invoices->hasPages())
            <div class="py-2 px-4 border-t border-slate-200">
                {{ $invoices->links() }}
            </div>
        @endif

        <x-loader.spinner target="sortBy, search, previousPage, nextPage" />
    </section>

   {{-- Espace qui apparait lors d'un bulk-actions --}}
    <div x-cloak x-show="$wire.selectedInvoiceIds.length > 0" class="max-sm:h-36 h-20"></div>

    {{-- Modales --}}
    <x-invoices.modal.prewiew :$showInvoicePreviewModal :$filePath :$fileExtension :$fileName />
    <x-invoices.modal.delete :$showDeleteFormModal :$filePath :$fileExtension :$fileName/>
</div>
