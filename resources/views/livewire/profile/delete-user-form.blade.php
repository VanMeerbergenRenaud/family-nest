<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;

use Livewire\Attributes\Validate;
use Livewire\Component;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

state(['password' => '']);

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

    <button type="button" x-data @click.prevent="$dispatch('open-modal')">
        {{ __('Supprimer le compte') }}
    </button>

    <div x-data="{ open: false }" x-on:open-modal.window="open = true">
        <div x-show="open" x-cloak>
            <form wire:submit.prevent="deleteUser">
                @csrf

                <h2 role="heading" aria-level="2">
                    {{ __('Êtes-vous sûr de vouloir supprimer votre compte ?') }}
                </h2>

                <p>
                    {{ __('Dès que votre compte est supprimé, toutes ses ressources et données seront définitivement supprimées. Veuillez saisir votre mot de passe pour confirmer que vous souhaitez supprimer définitivement votre compte.') }}
                </p>

                <div>
                    <x-form.field-password
                        label="Mot de passe"
                        name="password"
                        model="password"
                        autofocus
                        required
                    />
                </div>

                <div>
                    <button type="button" @click.prevent="open = false">
                        {{ __('Annuler') }}
                    </button>

                    <button type="submit">
                        {{ __('Supprimer le compte') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
