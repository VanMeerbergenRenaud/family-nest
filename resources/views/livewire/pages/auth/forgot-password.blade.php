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

<div>
    <p>
        {{ __('Vous avez oublié votre mot de passe ? Aucun problème. Communiquez-nous simplement votre adresse e-mail et nous vous enverrons par e-mail un lien de réinitialisation de mot de passe qui vous permettra d’en choisir un nouveau.') }}
    </p>

    <!-- Session Status -->
    @if (session('status'))
        <div>
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="sendPasswordResetLink">
        @csrf

        <!-- Email Address -->
        <div>
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

        <div>
            <button type="submit">
                {{ __('Envoyer le lien de réinitialisation') }}
            </button>
        </div>
    </form>
</div>
