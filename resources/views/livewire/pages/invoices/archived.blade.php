<div>
    <h1 class="text-2xl font-bold mb-6 ml-4">Factures archivées</h1>

    @if($archivedInvoices->isEmpty())
        <div class="bg-white p-6 rounded-lg">
            <p class="text-gray-700">Aucune facture archivée pour l'instant.</p>
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
                                    <button wire:click="showDeleteForm({{ $invoice->id }})" class="button-secondary">
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

        <!-- Modale pour supprimer d'une facture -->
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
        @endif

        @if($deletedWithSuccess)
            <x-flash-message
                icon="delete"
                title="Facture supprimée !"
                method="$set('deletedWithSuccess', false)"
            />
        @endif
    @endif
</div>
