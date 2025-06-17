<section class="mb-10">
    <h3 role="heading" aria-level="3" class="mb-3 pl-4 text-md-semibold text-gray-800 dark:text-white">
        {{ __('Factures récentes') }}
    </h3>

    @if($recentInvoices->isEmpty())
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl w-full border border-slate-200">
            <p class="text-gray-500 dark:text-gray-400">{{ __('Aucune facture récente.') }}</p>
        </div>
    @else
        <ul class="flex overflow-x-scroll gap-4 scrollbar-hidden">
            @foreach($recentInvoices as $invoice)
                <li wire:key="invoice-{{ $invoice->id }}"
                    class="pl-4 py-4 pr-3 min-w-fit h-fit rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden"
                >
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
                            <p class="text-sm-medium text-gray-900 dark:text-white">
                                {{ Str::limit($invoice->name, 16) }}
                            </p>
                            <p class="mt-1 w-max text-xs-regular text-gray-500 dark:text-gray-400">
                                {{ $invoice->dateForHumans($invoice->issued_date) }}
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

    {{-- Modales --}}
    <x-invoices.modal.prewiew :$showInvoicePreviewModal :$filePath :$fileExtension :$fileName />
    <x-invoices.modal.delete :$showDeleteFormModal :$filePath :$fileExtension :$fileName />

    <x-loader.spinner target="" position="fixed" />
</section>
