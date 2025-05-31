@props([
    'showModifyFamilyNameModal',
    'isAdmin',
])

<div>
    @if($showModifyFamilyNameModal && $isAdmin)
        <x-modal wire:model="showModifyFamilyNameModal">
            <x-modal.panel>
                <form wire:submit.prevent="updateFamilyName">
                    @csrf

                    <div class="p-6">
                        <!-- Titre et Description -->
                        <h2 role="heading" aria-level="2" class="text-xl-semibold">Modifier le nom de la famille</h2>
                        <p class="text-gray-500 text-sm mt-1 mb-2">
                            Changez le nom de votre famille
                        </p>

                        <x-divider/>

                        <!-- Formulaire pour le nouveau nom de la famille -->
                        <div class="grid grid-cols-1 gap-4 mt-6 mb-4">
                            <x-form.field
                                label="Nouveau nom de la famille"
                                name="newFamilyName"
                                model="form.newFamilyName"
                                placeholder="Exemple: Janssens"
                                :asterix="true"
                                autofocus
                            />
                        </div>
                    </div>

                    <x-modal.footer class="bg-white dark:bg-gray-800 border-t border-gray-400 dark:border-gray-700">
                        <div class="flex justify-end w-full gap-3">
                            <x-modal.close>
                                <button type="button" class="button-primary">
                                    {{ __('Annuler') }}
                                </button>
                            </x-modal.close>
                            <button type="button" wire:click="updateFamilyName" class="button-secondary"
                                    wire:loading.attr="disabled">
                                {{ __('Enregistrer') }}
                                <x-svg.validate class="text-white"/>
                            </button>
                        </div>
                    </x-modal.footer>
                </form>
            </x-modal.panel>
        </x-modal>
    @endif
</div>
