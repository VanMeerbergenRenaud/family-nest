@props([
    'showDeleteFormModal' => false,
    'filePath' => $filePath,
    'fileExtension' => $fileExtension,
    'fileName' => $fileName,
])

<div>
    <x-loader.spinner target="deleteDefinitelyInvoice" />

    @if($showDeleteFormModal)
        <x-modal wire:model="showDeleteFormModal">
            <x-modal.panel>
                <form wire:submit.prevent="deleteDefinitelyInvoice">
                    @csrf

                    <div x-data="{ confirmation: '' }">
                        <div class="flex gap-x-6 p-8">
                            <x-svg.advertising />

                            <div>
                                <h4 role="heading" aria-level="4" class="mb-4 text-xl-semibold">
                                    {{ __('Supprimer la facture') }}
                                </h4>
                                <p class="mt-2 text-md-regular text-gray-500">
                                    {{ __('Êtes-vous sûre de vouloir supprimer la facture') }}
                                    <strong class="font-semibold"> {{ $invoice->name ?? '' }}&nbsp;?</strong>
                                    {{ __('Toutes les données seront supprimées. Cette action est irréversible.') }}
                                </p>
                                <div class="mt-6 mb-2 flex flex-col gap-3">
                                    <label for="delete-definitely-invoice" class="text-sm-medium text-gray-800">
                                        {{ __('Veuillez tapper "CONFIRMER" pour confirmer la suppression.') }}
                                    </label>
                                    <input
                                        id="delete-definitely-invoice"
                                        type="text"
                                        x-model="confirmation"
                                        placeholder="CONFIRMER"
                                        class="py-2 px-3 text-sm-regular border border-gray-300 rounded-md w-[87.5%]"
                                        autofocus
                                    >
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
