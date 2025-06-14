<div>
    <div class="max-w-md mx-auto flex-center h-screen">
        <div class="p-6 h-fit bg-white dark:bg-gray-800 rounded-2xl overflow-hidden">

            <h1 role="heading" aria-level="1" class="pl-2 text-xl-semibold text-gray-900 dark:text-gray-100 mb-1">
                {{ __('Rejoindre la famille') }}
            </h1>

            <p class="pl-2 text-sm-regular text-gray-500 dark:text-gray-400 mb-4">
                {{ __('Créez votre compte pour rejoindre la famille') }}
            </p>

            <div class="flex items-center gap-4 px-4 py-3 mb-6 rounded-lg bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800">
                <x-svg.info class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0"/>
                <p class="text-blue-700 dark:text-blue-300">
                    Vous avez été invité à rejoindre la famille
                    <span class="font-medium">{{ $invitation->family->name }}</span>
                    en tant que {{ $invitation->relation }}.
                </p>
            </div>

            <x-flash-messages />

            <form wire:submit="register" class="flex flex-col gap-4">
                @csrf

                <x-form.field
                    label="Nom d'utilisateur"
                    name="name"
                    model="form.name"
                    placeholder="Nom"
                    :asterix="true"
                    autofocus
                    class="capitalize"
                />

                <x-form.field
                    label="Adresse mail"
                    name="email"
                    type="email"
                    model="form.email"
                    value="{{ $email }}"
                    placeholder="votre-mail@gmail.com"
                    class="lowercase"
                />

                <x-form.field-password
                    label="Créer votre mot de passe"
                    name="password"
                    model="form.password"
                    :asterix="true"
                />

                <div class="pt-4">
                    <button type="submit" class="button-tertiary w-full justify-center">
                        <x-svg.add2 class="text-white"/>
                        {{ __('Rejoindre la famille') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
