<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl-semibold text-gray-900 dark:text-gray-100 mb-1">{{ __('Rejoindre la famille') }}</h2>
            <p class="text-sm-regular text-gray-500 dark:text-gray-400 mb-6">{{ __('Créez votre compte pour rejoindre la famille') }}</p>

            <div
                class="flex items-center p-4 mb-6 rounded-xl bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800">
                <x-svg.info class="w-5 h-5 text-blue-500 dark:text-blue-400 mr-3 flex-shrink-0"/>
                <div class="text-sm-regular text-blue-700 dark:text-blue-300">
                    <p>
                        Vous avez été invité à rejoindre la famille
                        <span class="font-medium">{{ $invitation->family->name }}</span>
                        en tant que {{ $invitation->relation }}.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('family.invitation.accept', $invitation->token) }}" class="space-y-5">
                @csrf

                <x-form.field
                    label="Adresse E-Mail"
                    name="email"
                    value="{{ $email }}"
                    disabled
                />

                <x-form.field
                    label="Nom"
                    name="name"
                    :value="old('name')"
                    placeholder="Votre nom"
                    :asterix="true"
                    autofocus
                />

                <x-form.field-password
                    label="Mot de passe"
                    name="password"
                    placeholder="Votre mot de passe"
                    :asterix="true"
                />

                <div class="pt-4">
                    <button type="submit" class="button-tertiary w-full">
                        <x-svg.add2 class="text-white"/>
                        {{ __('Rejoindre la famille') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
