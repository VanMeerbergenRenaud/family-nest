@props([
    'showDeleteMembersModal',
    'isAdmin',
    'family',
])

<div>
    @if($showDeleteMembersModal && $isAdmin)
        <x-modal wire:model="showDeleteMembersModal">
            <x-modal.panel>
                <form wire:submit.prevent="deleteMembers">
                    @csrf

                    <div x-data="{ confirmation: '' }">
                        <div class="flex gap-x-6 p-8">
                            <x-svg.advertising/>

                            <div>
                                <h2 role="heading" aria-level="2" class="mb-4 text-xl-semibold">
                                    {{ __('Supprimer les membres de la famille') }}
                                </h2>
                                <p class="mt-2 text-md-regular text-gray-500">
                                    {{ __('Êtes-vous sûr de vouloir supprimer tous les membres de la famille') }}
                                    <strong class="font-semibold"> {{ $family->name ?? __('Nom de la famille') }}
                                        &nbsp;?</strong>
                                    {{ __('Tous les membres seront détachés de la famille, sauf vous. Cette action est irréversible.') }}
                                </p>
                                <div class="mt-6 mb-2 flex flex-col gap-3">
                                    <label for="delete-definitely-members" class="text-sm-medium text-gray-800">
                                        {{ __('Veuillez tapper "CONFIRMER" pour confirmer la suppression.') }}
                                    </label>
                                    <input x-model="confirmation" placeholder="CONFIRMER" type="text"
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
                                <button type="submit" class="button-danger" :disabled="confirmation !== 'CONFIRMER'"
                                        wire:loading.attr="disabled">
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
