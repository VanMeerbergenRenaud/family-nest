<div>
    <h1 class="text-2xl font-bold mb-6 ml-4">Factures archivées</h1>

    @if(empty($archivedInvoices))
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-gray-700">Aucune facture archivée trouvée.</p>
        </div>
    @else
        <div class="w-full overflow-x-auto rounded-lg border border-gray">
            <table class="w-full">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Émetteur</th>
                    <th>Montant</th>
                    <th>Date d'archivage</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($archivedInvoices as $invoice)
                        <tr>
                            <td>
                                <p>{{ $invoice->name }}</p>
                            </td>
                            <td>
                                <p>{{ $invoice->issuer_name }}</p>
                            </td>
                            <td>
                                <p>
                                    {{ number_format($invoice->amount, 2, ',', ' ') }} {{ $invoice->currency }}
                                </p>
                            </td>
                            <td>
                                <p>{{ $invoice->updated_at->format('d/m/Y H:i') }}</p>
                            </td>
                            <td>
                                <div class="flex space-x-4">
                                    <button wire:click="restoreInvoice({{ $invoice->id }})" class="button-primary">
                                        Restaurer
                                    </button>
                                    <button wire:click="deleteDefinitelyInvoice({{ $invoice->id }})" class="button-secondary">
                                        Supprimer définitivement
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($archivedInvoices->hasPages())
            <div class="mt-4">
                {{ $archivedInvoices->links() }}
            </div>
        @endif
    @endif
</div>
