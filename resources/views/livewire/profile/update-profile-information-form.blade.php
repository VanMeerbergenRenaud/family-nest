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

<section class="p-6">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-400" role="heading" aria-level="2">
            {{ __('Informations du profil') }}
        </h2>

        <p class="text-sm text-gray-600">
            {{ __("Mettez à jour les informations de votre compte et votre adresse email.") }}
        </p>
    </div>

    <form wire:submit="updateProfileInformation">
        @csrf

        <div class="mb-8 flex flex-col gap-4">
            <x-form.field label="Nom" name="name" model="name" placeholder="John Doe"/>
            <x-form.field label="Adresse email" name="email" model="email" type="email"
                          placeholder="renseignez votre adresse email"/>
        </div>

            @if (auth()->user() instanceof MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
            <div class="mt-2 text-sm text-gray-600">
                    <p>
                        {{ __('Cette nouvelle adresse email n’a pas encore été vérifiée.') }}

                        <button type="button" wire:click.prevent="sendVerification"
                                class="text-indigo-600 hover:text-indigo-900">
                            {{ __('Cliquez ici pour envoyer un lien de vérification.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-sm text-green-600">
                            {{ __('Un nouveau lien de vérification a été envoyé à l’adresse email que vous avez fournie lors de votre inscription.') }}
                        </p>
                    @endif
                </div>
            @endif

            <div class="flex justify-start mt-6">
                <button type="button"
                        class="mr-4 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Annuler') }}
                </button>

                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Sauvegarder') }}
                </button>

            <x-action-message on="profile-updated">
                <p class="text-sm text-gray-800">{{ __('Profil mis à jour.') }}</p>
                <button type="button" @click="actionMessage = false" class="text-gray-600 hover:text-gray-800">
                    <x-svg.cross/>
                </button>
            </x-action-message>
        </div>
    </form>
</section>
