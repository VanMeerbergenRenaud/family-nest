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

$directLogin = function () {
    $user = Auth::user();

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    Auth::login($user);
    $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
};

?>

<x-auth-template title="Vérification">
    <div class="text-md-regular text-gray-700 px-4 mt-[-1rem]">

        <!-- Description -->
        <p class="pl-2">
            {{ __('Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer par e-mail ? Si vous n’avez pas reçu l’e-mail, nous vous en enverrons un autre avec plaisir.') }}
        </p>

        <!-- Session status -->
        @if (session('status') == 'verification-link-sent')
            <p class="mt-4 mb-6 bg-green-50 border border-green-200 py-2 px-4 gap-4 rounded-md text-sm text-green-700 dark:bg-green-100 dark:border-green-300 dark:text-green-600 flex items-center">
                <x-svg.success class="h-4 w-4" />
                {{ __('Un nouveau lien de vérification a été envoyé à l’adresse e-mail que vous avez fournie lors de votre inscription.') }}
            </p>
        @endif

        <!-- Liens de vérification ou de déconnexion -->
        <div class="mt-5 flex flex-wrap gap-2">
            <button type="button" wire:click="sendVerification" class="button-primary">
                {{ __('Renvoyer un nouveau.') }}
            </button>

            <button type="button" wire:click="logout" class="button-secondary">
                {{ __('Se déconnecter') }}
            </button>

            {{-- TODO : SUPRESS PROD : Connexion sans vérification --}}
            <button class="button-tertiary" wire:click="directLogin">
                {{ __('Se connecter sans vérification') }}
            </button>
        </div>
    </div>
</x-auth-template>

