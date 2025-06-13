<x-auth-template title="Réinitialisation du mot de passe">

    <form wire:submit="resetPassword">
        @csrf

        <div class="flex flex-col gap-4">

            <!-- Email -->
            <x-form.field
                label="Adresse mail"
                name="email"
                type="email"
                model="email"
                placeholder="votre-email@gmail.com"
                autocomplete="email"
                autofocus
                required
                class="lowercase"
            />

            <!-- Password -->
            <x-form.field-password
                label="Mot de passe"
                name="password"
                model="password"
                autocomplete="new-password"
                required
            />

            <!-- Confirm password -->
            <x-form.field-password
                label="Confirmer le mot de passe"
                name="password_confirmation"
                model="password_confirmation"
                autocomplete="new-password"
                required
            />
        </div>

        <button type="submit" class="mt-6 button-secondary">
            {{ __('Réinitialiser le mot de passe') }}
        </button>
    </form>
</x-auth-template>
