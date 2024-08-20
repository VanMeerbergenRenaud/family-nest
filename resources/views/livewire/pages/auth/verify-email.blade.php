<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\layout;

layout('layouts.guest');

$sendVerification = function () {
    if (Auth::user()->hasVerifiedEmail()) {
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

        return;
    }

    Auth::user()->sendEmailVerificationNotification();

    Session::flash('status', 'verification-link-sent');
};

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<div>
    <p>
        {{ __('Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer par e-mail ? Si vous n’avez pas reçu l’e-mail, nous vous en enverrons un autre avec plaisir.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <p>
            {{ __('Un nouveau lien de vérification a été envoyé à l’adresse e-mail que vous avez fournie lors de votre inscription.') }}
        </p>
    @endif

    <div>
        <button type="button" wire:click="sendVerification">
            {{ __('Renvoyer le lien de vérification') }}
        </button>

        <button type="button" wire:click="logout">
            {{ __('Se déconnecter') }}
        </button>
    </div>
</div>
