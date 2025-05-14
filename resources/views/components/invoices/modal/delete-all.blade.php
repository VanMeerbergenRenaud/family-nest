@props([
    'showDeleteAllFormModal' => false,
    'filterType' => null,
])

<div>
    <x-loader.spinner target="deleteDefinitelyAllInvoice" />

    @if($showDeleteAllFormModal)
        <x-modal wire:model="showDeleteAllFormModal">
            <x-modal.panel>
                <form wire:submit.prevent="deleteDefinitelyAllInvoice">
                    @csrf

                    <div x-data="{ confirmation: '' }">
                        <div class="flex gap-x-6 p-8">
                            <x-svg.advertising/>
                            <div>
                                <h2 role="heading" aria-level="2" class="mb-4 text-xl-semibold">
                                    {{ __('Supprimer les archives') }}
                                </h2>
                                <p class="mt-2 text-md-regular text-gray-500">
                                    @php
                                        $message = 'Êtes-vous sûr de vouloir supprimer définitivement toutes les factures archivées';
                                        if($filterType === 'personal') $message .= ' personnelles';
                                    @endphp
                                    {{ __($message) }}<strong class="font-semibold">&nbsp;?</strong>
                                    {{ __('Toutes les données seront supprimées. Cette action est irréversible.') }}
                                </p>
                                <div class="mt-6 mb-2 flex flex-col gap-3">
                                    <label for="delete-definitely-all-invoices" class="text-sm-medium text-gray-800">
                                        {{ __('Veuillez taper "VIDER" pour confirmer la suppression.') }}
                                    </label>
                                    <input
                                        id="delete-definitely-all-invoices"
                                        type="text"
                                        x-model="confirmation"
                                        placeholder="VIDER"
                                        class="py-2 px-3 text-sm-regular border border-gray-300 rounded-md w-[87.5%]"
                                        autofocus
                                    >
                                </div>
                            </div>
                        </div>

                        <x-modal.footer>
                            <x-modal.close>
                                <button type="button" class="button-secondary">{{ __('Annuler') }}</button>
                            </x-modal.close>
                            <x-modal.close>
                                <button type="submit" class="button-danger" :disabled="confirmation !== 'VIDER'">
                                    {{ __('Supprimer toutes les archives') }}
                                </button>
                            </x-modal.close>
                        </x-modal.footer>
                    </div>
                </form>
            </x-modal.panel>
        </x-modal>
    @endif
</div>
