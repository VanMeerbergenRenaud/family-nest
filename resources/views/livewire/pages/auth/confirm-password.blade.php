<x-auth-template title="Mot de passe oublié">
    <div class="text-md-regular text-gray-700 px-4 mt-[-1rem]">

        <p>
            {{ __('Il s’agit d’une zone sécurisée de l’application. Veuillez confirmer votre mot de passe avant de continuer.') }}
        </p>

        <form wire:submit="confirmPassword" class="mt-4">
            @csrf

            <div class="flex flex-col gap-4">
                <x-form.field-password
                    label="Mot de passe"
                    name="password"
                    model="password"
                    autocomplete="current-password"
                    required
                />
            </div>

            <button type="submit" class="mt-4 button-secondary">
                {{ __('Confirmer') }}
            </button>
        </form>
    </div>
</x-auth-template>
