{{-- Tableau regroupant toutes les factures --}}
<section class="relative w-full overflow-hidden bg-white dark:bg-gray-800 rounded-2xl border border-slate-200">

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 pl-6 border-b border-gray-200 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center gap-2">
            <x-dashboard.search/>

            <div class="ml-4 text-gray-700 text-sm">
                Résultats: {{ \Illuminate\Support\Number::format($invoices->total()) }}
                {{ $invoices->total() <= 1 ? 'facture' : 'factures' }}
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
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

    <div class="w-full overflow-x-auto scrollbar-hidden">
        <table class="w-full" aria-labelledby="tableTitle">
            <thead>
            <tr>
                {{-- Checkboxes... --}}
                <td class="p-0 pl-2">
                    <x-dashboard.check-all/>
                </td>

                @if($visibleColumns['name'] ?? false)
                    <th scope="col" class="min-w-[10rem]">
                        <x-dashboard.sortable column="name" :$sortCol :$sortAsc>
                            <span class="text-xs-semibold">Nom de la facture</span>
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

                    @if($visibleColumns['name'] ?? false)
                        <td>
                            {{ $invoice->name ?? 'Non spécifié' }}
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

                <x-dashboard.bulk-actions :$invoices />
            @endforeach
            @if($invoices->count() <= 5)
                <tr>
                    <td colspan="100%" class="h-80">
                        @if($invoices->count() < 5 && $invoices->count() > 0)
                            <div class="h-[15rem]"></div>
                        @elseif($invoices->isEmpty())
                            <div class="flex-center flex-col h-[20rem]">
                                <x-svg.error class="text-gray-400 w-6 h-6"/>
                                <h2 class="mt-4 text-lg-semibold text-gray-900">
                                    Aucune facture trouvée
                                </h2>
                                <p class="text-center mt-2 text-sm text-gray-500">
                                    Vous n'avez pas encore de factures.<br>
                                    Veuillez commencer par ajouter une facture !
                                </p>
                                <a href="{{ route('invoices.create') }}" title="Vers la page de création d'une facture"
                                   class="mt-4 button-secondary" wire:navigate>
                                    <x-svg.add class="mr-1 text-white"/>
                                    Ajouter une facture
                                </a>
                            </div>
                        @endif
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

    {{-- Table loading spinners... --}}
    <div wire:loading wire:target="sortBy, search, previousPage, nextPage" class="absolute inset-0 bg-white opacity-50"></div>
    <div wire:loading.flex wire:target="sortBy, search, previousPage, nextPage" class="flex-center absolute inset-0">
        <x-svg.spinner class="text-gray-500 size-7"/>
    </div>
</section>
