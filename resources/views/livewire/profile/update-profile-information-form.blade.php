<?php

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

use function Livewire\Volt\state;

state([
    'name' => fn () => auth()->user()->name,
    'email' => fn () => auth()->user()->email
]);

$updateProfileInformation = function () {
    $user = Auth::user();

    $validated = $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
    ]);

    $user->fill($validated);

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();

    $this->dispatch('profile-updated', name: $user->name);
};

$sendVerification = function () {
    $user = Auth::user();

    if ($user->hasVerifiedEmail()) {
        $this->redirectIntended(default: route('dashboard', absolute: false));

        return;
    }

    $user->sendEmailVerificationNotification();

    Session::flash('status', 'verification-link-sent');
};

?>

<section>
    <header>
        <h2 role="heading" aria-level="2">
            {{ __('Informations du profil') }}
        </h2>

        <p>
            {{ __("Mettez à jour les informations de votre compte et votre adresse email.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation">
        @csrf

        <div>
            <x-form.field
                label="Nom"
                name="name"
                model="name"
                autocomplete="name"
                required
            />
        </div>

        <div>
            <x-form.field
                label="Adresse email"
                name="email"
                type="email"
                model="email"
                autocomplete="email"
                required
            />

            @if (auth()->user() instanceof MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p>
                        {{ __('Cette nouvelle adresse email n’a pas encore été vérifiée.') }}

                        <button type="button" wire:click.prevent="sendVerification">
                            {{ __('Cliquez ici pour envoyer un lien de vérification.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p>
                            {{ __('Un nouveau lien de vérification a été envoyé à l’adresse email que vous avez fournie lors de votre inscription.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <button type="submit">{{ __('Sauvegarder') }}</button>

            <x-action-message on="profile-updated">
                {{ __('Profil mis à jour.') }}
            </x-action-message>
        </div>
    </form>
</section>
