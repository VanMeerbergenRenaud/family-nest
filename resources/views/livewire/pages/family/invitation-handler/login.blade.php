<div>
    <div class="max-w-md mx-auto flex-center h-screen">
        <div class="p-6 h-fit bg-white dark:bg-gray-800 rounded-2xl overflow-hidden">

            <h1 role="heading" aria-level="1" class="pl-2 text-xl-semibold text-gray-900 dark:text-gray-100 mb-1">
                {{ __('Rejoindre la famille') }}
            </h1>

            <p class="pl-2 text-sm-regular text-gray-500 dark:text-gray-400 mb-4">
                {{ __('Connectez-vous pour rejoindre la famille') }}
            </p>

            <div
                class="flex items-center gap-4 px-4 py-3 mb-6 rounded-lg bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800">
                <x-svg.info class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0"/>
                <p class="text-blue-700 dark:text-blue-300">
                    Vous avez été invité à rejoindre la famille
                    <span class="font-medium">{{ $invitation->family->name }}</span>
                    en tant que {{ $invitation->relation }}.
                    Un compte existe déjà avec l'adresse <span class="font-medium">{{ $email }}</span>.
                </p>
            </div>

            <x-flash-messages/>

            <form wire:submit="attemptLogin" class="flex flex-col gap-4">
                @csrf

                <x-form.field
                    label="Adresse mail"
                    name="email"
                    type="email"
                    value="{{ $email }}"
                    placeholder="votre-email@gmail.com"
                    readonly
                    disabled
                    class="lowercase bg-gray-50"
                />

                <x-form.field-password
                    label="Mot de passe"
                    name="password"
                    model="password"
                    placeholder="Votre mot de passe"
                    :asterix="true"
                    autofocus
                />

                <div class="mt-2 md:px-2 flex items-center justify-between">
                    <x-form.checkbox-input
                        label="Se souvenir de moi"
                        name="remember"
                        wire:model.blur="remember"
                        checked
                    />
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="ml-3 min-w-fit text-sm-medium underline"
                           title="Vers la page de réinitialisation de mot de passe" wire:navigate>
                            {{ __("Mot de passe oublié ?") }}
                        </a>
                    @endif
                </div>

                <div class="pt-4">
                    <button type="submit" class="button-tertiary w-full justify-center">
                        {{ __('Rejoindre la famille') }}
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-sm text-gray-500">
                <p>L'invitation expire le {{ $invitation->expires_at->format('d/m/Y') }}</p>
            </div>

            <div class="mt-4 text-center">
                <button wire:click="redirectToWelcome" class="text-sm text-blue-600 hover:underline">
                    {{ __('Retourner à l\'accueil') }}
                </button>
            </div>
        </div>
    </div>
</div>
