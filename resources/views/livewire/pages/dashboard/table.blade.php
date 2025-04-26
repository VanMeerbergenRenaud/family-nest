<div class="flex flex-col gap-8">
    <div class="flex flex-wrap items-center gap-2">
        <x-dashboard.search />

        <div class="ml-4 text-gray-700 text-sm">
            Résultats: {{ \Illuminate\Support\Number::format($invoices->total()) }} factures
        </div>
    </div>

    <div class="relative bg-white border border-slate-200 rounded-xl overflow-y-hidden">
        <table class="relative overflow-x-auto">
            <thead>
                <tr>
                    {{-- Checkboxes... --}}
                    <td class="p-0 pl-2">
                        <x-dashboard.check-all />
                    </td>

                    <th>
                        <x-dashboard.sortable column="name" :$sortCol :$sortAsc>
                            <span class="text-sm-medium">Nom de la facture</span>
                        </x-dashboard.sortable>
                    </th>

                    <th>
                        <x-dashboard.sortable column="type" :$sortCol :$sortAsc>
                            <span class="text-sm-medium">Type</span>
                        </x-dashboard.sortable>
                    </th>

                    <th>
                        <x-dashboard.sortable column="amount" :$sortCol :$sortAsc>
                            <span class="text-sm-medium">Montant</span>
                        </x-dashboard.sortable>
                    </th>

                    <th>
                        <x-dashboard.sortable column="payment_status" :$sortCol :$sortAsc>
                            <span class="text-sm-medium">Statut de paiement</span>
                        </x-dashboard.sortable>
                    </th>

                    <th>
                        <x-dashboard.sortable column="payment_due_date" :$sortCol :$sortAsc>
                            <span class="text-sm-medium">Date d'échéance</span>
                        </x-dashboard.sortable>
                    </th>

                    <th>
                        {{-- Dropdown menus... --}}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                    <tr wire:key="{{ $invoice->id }}">
                        <td class="p-0 pl-2">
                            <label class="relative flex items-center cursor-pointer whitespace-nowrap p-4 w-fit">
                                <input wire:model="selectedInvoiceIds" value="{{ $invoice->id }}" type="checkbox" class="peer h-4 w-4 cursor-pointer transition-all appearance-none rounded border border-slate-300 checked:bg-blue-700 checked:border-blue-700" />
                                <span class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none">
                                    <x-svg.check class="text-white h-3 w-3" />
                                </span>
                            </label>
                        </td>

                        <td>
                            {{ $invoice->name }}
                        </td>

                        <td>
                            {{ $invoice->type }}
                        </td>

                        <td>
                            {{ $invoice->amount }}
                            <span class="text-xs text-gray-400">{{ \App\Enums\CurrencyEnum::from($invoice->currency)->symbolWithIndicator() }}</span>
                        </td>

                        <td>
                            <span class="rounded-full py-0.5 pl-2 pr-1 inline-flex font-medium items-center gap-1 text-gray-600 text-xs bg-gray-100 opacity-75">
                                {{ $invoice->payment_status }}
                            </span>
                        </td>

                        <td>
                            @if($invoice->payment_due_date)
                                {{ $invoice->dateForHumans($invoice->payment_due_date) }}
                            @else
                                Non définie
                            @endif
                        </td>

                        <td class="grid justify-end text-right">
                            <x-menu>
                                <x-menu.button class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <x-svg.dots class="w-5 h-5 text-gray-500"/>
                                </x-menu.button>

                                <x-menu.items>
                                    <x-menu.item
                                        wire:click="refund({{ $invoice->id }})"
                                        wire:confirm="Are you sure you want to refund this invoice?"
                                    >
                                        Voir
                                    </x-menu.item>

                                    <x-menu.item
                                        wire:click="archive({{ $invoice->id }})"
                                        wire:confirm="Are you sure you want to archive this invoice?"
                                    >
                                        Archiver
                                    </x-menu.item>
                                </x-menu.items>
                            </x-menu>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Table loading spinners... --}}
        <div wire:loading wire:target="sortBy, search, previousPage, nextPage" class="absolute inset-0 bg-white opacity-50"></div>
        <div wire:loading.flex wire:target="sortBy, search, previousPage, nextPage" class="flex-center absolute inset-0">
            <x-svg.spinner class="text-gray-500 size-7" />
        </div>

        {{-- Pagination... --}}
        <div class="px-4">
            {{ $invoices->links() }}
        </div>

        {{-- Table empty state... --}}
        @if($invoices->count() < 5 && $invoices->count() > 0)
            <div class="h-[15rem]"></div>
        @elseif($invoices->isEmpty())
            <div class="flex-center flex-col h-[20rem]">
                <x-svg.error class="text-gray-400 w-6 h-6" />
                <h2 class="mt-4 text-lg-semibold text-gray-900">
                    Aucune facture trouvée
                </h2>
                <p class="text-center mt-2 text-sm text-gray-500 max-w-sm">
                    Vous n'avez pas encore de factures. Veuillez commencer par ajouter une facture !
                </p>
                <a href="{{ route('invoices.create') }}" title="Vers la page de création d'une facture" class="mt-4 button-secondary" wire:navigate>
                    <x-svg.add  class="mr-1 text-white" />
                    Ajouter une facture
                </a>
            </div>
        @endif
    </div>
</div>
