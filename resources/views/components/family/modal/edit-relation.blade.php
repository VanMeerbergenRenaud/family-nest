@props([
    'showChangeRelationModal',
    'relationMemberId',
    'familyRelationEnum',
])

@if($showChangeRelationModal && $relationMemberId)
    <x-modal wire:model="showChangeRelationModal">
        <x-modal.panel>

            <div class="flex flex-col gap-4 p-8">
                <h3 role="heading" aria-level="3" class="text-lg font-semibold">
                    {{ __('Changer la relation') }}
                </h3>

                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Sélectionnez la nouvelle relation pour '. $this->members->firstWhere('id', $relationMemberId)->name . '.') }}
                    {{ __('Cette action mettra à jour la relation de ce membre dans votre famille.') }}
                </p>

                <div class="flex flex-wrap gap-3">
                    @foreach($familyRelationEnum::cases() as $relationOption)
                        @php
                            $member = $this->members->firstWhere('id', $relationMemberId);
                            $isCurrentRelation = $member && $member->relation === $relationOption->value;
                            $color = $isCurrentRelation ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'hover:bg-gray-50';
                        @endphp

                        <button
                            type="button"
                            wire:loading.attr="disabled"
                            wire:click="changeMemberRelation({{ $relationMemberId }}, '{{ $relationOption->value }}')"
                            class="button-primary w-fit transition-colors {{ $color }}"
                        >
                            <p class="text-sm-semibold">{{ $relationOption->label() }}</p>
                        </button>
                    @endforeach
                </div>
            </div>

            <x-modal.footer>
                <x-modal.close>
                    <button type="button" class="button-secondary">
                        Annuler
                    </button>
                </x-modal.close>
            </x-modal.footer>
        </x-modal.panel>
    </x-modal>
@endif
