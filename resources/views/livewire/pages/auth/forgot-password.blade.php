<?php

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('layouts.guest');

state(['email' => '']);

rules(['email' => ['required', 'string', 'email']]);

$sendPasswordResetLink = function () {
    $this->validate();

    // We will send the password reset link to this user. Once we have attempted
    // to send the link, we will examine the response then see the message we
    // need to show to the user. Finally, we'll send out a proper response.
    $status = Password::sendResetLink(
        $this->only('email')
    );

    if ($status != Password::RESET_LINK_SENT) {
        $this->addError('email', __($status));

        return;
    }

    $this->reset('email');

    Session::flash('status', __($status));
};

?>

<x-auth-template title="Mot de passe oublié">
    <div class="text-md-regular text-gray-700 px-4 mt-[-1rem]">

        <!-- Description -->
        <p>
            {{ __('Vous avez oublié votre mot de passe ? Aucun problème. Communiquez-nous simplement votre adresse e-mail et nous vous enverrons par e-mail un lien de réinitialisation de mot de passe qui vous permettra d’en choisir un nouveau.') }}
        </p>

        <!-- Session status -->
        @if(session('status'))
            <div class="mt-4 mb-6 bg-green-50 border border-green-200 py-2 px-4 gap-4 rounded-md text-sm text-green-700 dark:bg-green-100 dark:border-green-300 dark:text-green-600 flex items-center">
                <x-svg.success class="h-4 w-4" />
                {{ session('status') }}
            </div>
        @endif

        <!-- Formulaire de réinitialisation -->
        <form wire:submit="sendPasswordResetLink" class="mt-4">
            @csrf

            <div class="flex flex-col gap-4">
                <!-- Adresse mail -->
                <x-form.field
                    label="Adresse e-mail"
                    name="email"
                    type="email"
                    model="email"
                    placeholder="john.doe@gmail.com"
                    autofocus
                    required
                />
            </div>

            <button type="submit" class="mt-4 button-secondary">
                {{ __('Envoyer le lien de réinitialisation') }}
            </button>
        </form>
    </div>
</x-auth-template>
