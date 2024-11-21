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

<section>
    <header>
        <h2 role="heading" aria-level="2">
            {{ __('Supprimer le compte') }}
        </h2>

        <p>
            {{ __('Dès que votre compte est supprimé, toutes ses ressources et données seront définitivement supprimées. Avant de supprimer votre compte, veuillez télécharger') }}
        </p>
    </header>

    <x-modal wire:model="showModal">
        <x-modal.open>
            <button type="button" class="button--danger">
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
