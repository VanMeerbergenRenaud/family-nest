<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;

use Livewire\Attributes\Validate;
use Livewire\Component;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

state(['password' => '', 'showModal' => false]);

$openModal = function () {
    $this->showModal = true;
};

rules(['password' => ['required', 'string', 'current_password']]);

$deleteUser = function (Logout $logout) {
    $this->validate();

    tap(Auth::user(), $logout(...))->delete();

    $this->redirect('/', navigate: true);
};

?>

<section class="p-6 bg-white rounded-lg">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900" role="heading" aria-level="2">
            {{ __('Supprimer le compte') }}
        </h2>

        <p class="text-sm text-gray-600">
            {{ __('Dès que votre compte est supprimé, toutes ses ressources et données seront définitivement supprimées. Avant de supprimer votre compte, veuillez télécharger') }}
        </p>
    </div>

    <x-modal wire:model="showModal">
        <x-modal.open>
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('Supprimer le compte') }}
            </button>
        </x-modal.open>

        <x-modal.panel>
            <form wire:submit.prevent="deleteUser" class="form">
                @csrf

                <div class="form__content">
                    <h3 role="heading" aria-level="3" class="form__content__heading">
                        {{ __('Êtes-vous sûr de vouloir supprimer votre compte ?') }}
                    </h3>

                    <p class="text">
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
                        <button type="button" class="cancel">
                            {{ __('Annuler') }}
                        </button>
                    </x-modal.close>

                    <button type="submit" class="delete">
                        {{ __('Supprimer le compte') }}
                    </button>
                </x-modal.footer>
            </form>
        </x-modal.panel>
    </x-modal>
</section>
