@props([
    'showCreateFamilyModal',
])

<div>
    @if($showCreateFamilyModal)
        <x-modal wire:model="showCreateFamilyModal">
            <x-modal.panel>
                <form wire:submit.prevent="createFamily">
                    @csrf

                    <div class="p-6">
                        <!-- Titre et Description -->
                        <h2 role="heading" aria-level="2" class="text-xl-semibold">Créer une famille</h2>
                        <p class="text-gray-500 text-sm mt-1 mb-2">
                            Pour commencer à gérer vos dépenses ensemble
                        </p>

                        <x-divider/>

                        <!-- Formulaire simple pour le nom de la famille -->
                        <div class="grid grid-cols-1 gap-4 mt-6 mb-4">
                            <x-form.field
                                label="Nom de la famille"
                                name="familyName"
                                model="form.familyName"
                                placeholder="Exemple: Janssens"
                                :asterix="true"
                                autofocus
                                required
                                class="capitalize"
                            />
                        </div>
                    </div>

                    <x-modal.footer class="bg-white dark:bg-gray-800 border-t border-gray-400 dark:border-gray-700">
                        <div class="flex justify-end w-full gap-3">
                            <x-modal.close>
                                <button type="button" class="button-secondary">
                                    {{ __('Annuler') }}
                                </button>
                            </x-modal.close>
                            <button type="submit" class="button-primary" wire:loading.attr="disabled">
                                {{ __('Valider le nom') }}
                                <x-svg.validate/>
                            </button>
                        </div>
                    </x-modal.footer>
                </form>
            </x-modal.panel>
        </x-modal>
    @endif
</div>
