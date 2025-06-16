@props([
    'showAddMemberModal',
    'isAdmin',
    'permissionOptions',
    'relationOptions',
])

<div>
    @if($showAddMemberModal && $isAdmin)
        <x-modal wire:model="showAddMemberModal">
            <x-modal.panel>
                <form>
                    @csrf

                    <div class="flex gap-x-6 p-6">
                        <div class="w-full space-y-6">
                            <!-- Titre et Description -->
                            <div class="text-center space-y-3">
                                <!-- Avatar Image -->
                                <div class="flex-center mt-4 mb-6">
                                    <img src="{{ asset('img/users/three.png') ?? null }}"
                                         class="h-16 w-auto rounded-full object-cover bg-transparent"
                                         alt="Exemple de 3 avatars"
                                         loading="lazy"
                                    >
                                </div>
                                <h2 role="heading" aria-level="2" class="display-xs-semibold">Inviter un membre</h2>
                                <p class="text-gray-600 dark:text-gray-400 px-4 lg:px-12">
                                    Invitez un membre de votre famille ou un proche pour mieux gérer vos dépenses
                                    ensemble.
                                </p>
                            </div>

                            <!-- Formulaire d'ajout de membre -->
                            <div class="grid grid-cols-1 gap-4 my-4">

                                <x-form.field
                                    label="Adresse email"
                                    name="memberEmail"
                                    model="form.memberEmail"
                                    placeholder="exemple@gmail.com"
                                    type="email"
                                    :asterix="true"
                                />

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-form.select
                                        label="Rôle dans la famille"
                                        name="memberPermission"
                                        model="form.memberPermission"
                                        :asterix="true"
                                    >
                                        {{-- Si admin, peut attribuer tous les rôles --}}
                                        @if($isAdmin)
                                            @foreach($permissionOptions as $value => $label)
                                                <option value="{{ $value }}">
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                            {{-- Si editor, ne peut pas attribuer le rôle admin --}}
                                        @else
                                            @foreach($permissionOptions as $value => $label)
                                                @if($value !== $familyPermissionEnum::Admin->value)
                                                    <option value="{{ $value }}">
                                                        {{ $label }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </x-form.select>

                                    <x-form.select
                                        label="Relation avec vous"
                                        name="memberRelation"
                                        model="form.memberRelation"
                                        :asterix="true"
                                    >
                                        @foreach($relationOptions as $value => $label)
                                            <option value="{{ $value }}">
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </x-form.select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <x-modal.footer class="bg-white dark:bg-gray-800 border-t border-gray-400 dark:border-gray-700">
                        <div class="flex justify-end w-full gap-3">
                            <x-modal.close>
                                <button type="button" class="button-primary">
                                    {{ __('Annuler') }}
                                </button>
                            </x-modal.close>
                            <button type="button" wire:click="sendInvitation" class="button-secondary gap-2"
                                    wire:loading.attr="disabled">
                                <x-svg.send class="text-white"/>
                                {{ __('Inviter') }}
                            </button>
                        </div>
                    </x-modal.footer>
                </form>
            </x-modal.panel>
        </x-modal>
    @endif
</div>
