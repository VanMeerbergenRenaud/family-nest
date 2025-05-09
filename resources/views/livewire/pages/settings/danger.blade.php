<div>
    <x-empty-state
        title="Zone de suppression du compte"
        description="Dès que votre compte est supprimé, toutes ses ressources et données seront définitivement supprimées. Avant de supprimer votre compte, veuillez télécharger toutes les données que vous souhaitez conserver."
    >
        <x-modal wire:model="showModal">
            <x-modal.open>
                <button type="button" wire:click="openModal" class="button-danger">
                    {{ __('Supprimer le compte') }}
                </button>
            </x-modal.open>

            <x-modal.panel>
                <form wire:submit.prevent="deleteUser">
                    @csrf

                    <div class="flex flex-col gap-4 p-8">
                        <h3 role="heading" aria-level="3" class="text-lg font-semibold">
                            {{ __('Êtes-vous sûr de vouloir supprimer votre compte ?') }}
                        </h3>

                        <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Une fois votre compte supprimé, toutes les données et ressources associées seront supprimées de manière permanente. Veuillez saisir votre mot de passe pour confirmer que vous souhaitez supprimer votre compte de manière permanente.') }}
                        </p>

                        <x-form.field-password
                            label="Mot de passe"
                            name="password"
                            type="password"
                            placeholder="Inscrivez votre mot de passe"
                            model="password"
                            autofocus
                            required
                        />
                    </div>

                    <x-modal.footer>
                        <x-modal.close>
                            <button type="button" class="button-secondary">
                                {{ __('Annuler') }}
                            </button>
                        </x-modal.close>

                        <button type="submit" class="button-danger">
                            {{ __('Supprimer le compte') }}
                        </button>
                    </x-modal.footer>
                </form>
            </x-modal.panel>
        </x-modal>

        <x-loader.spinner target="deleteUser" />
    </x-empty-state>
</div>
