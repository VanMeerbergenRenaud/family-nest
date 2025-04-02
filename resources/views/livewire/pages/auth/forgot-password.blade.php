<x-auth-template title="Mot de passe oublié">
    <div class="text-md-regular text-gray-700 px-4 mt-[-1rem]">

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

        <form wire:submit="sendPasswordResetLink" class="mt-4">
            @csrf

            <div class="flex flex-col gap-4">
                <x-form.field
                    label="Adresse e-mail"
                    name="email"
                    type="email"
                    model="email"
                    placeholder="votre-email@gmail.com"
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
