<x-auth-template title="Vérification">
    <div class="text-md-regular text-gray-700 px-4 mt-[-1rem]">

        <p class="pl-2">
            {{ __('Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse mail en cliquant sur le lien que nous venons de vous envoyer par mail ? Si vous n’avez pas reçu le mail, nous vous en enverrons un autre avec plaisir.') }}
        </p>

        <!-- Session status -->
        @if (session('status') == 'verification-link-sent')
            <p class="my-4 bg-green-50 border border-green-200 py-2 px-4 gap-3 rounded-md text-sm text-green-700 dark:bg-green-100 dark:border-green-300 dark:text-green-600 flex items-start">
                <x-svg.success class="w-8 h-6 text-green-600" />
                {{ __('Un nouveau lien de vérification a été envoyé à l’adresse mail que vous avez fournie lors de votre inscription.') }}
            </p>
        @endif

        <div class="mt-5 flex flex-wrap gap-2">
            <!-- Verification link -->
            <button type="button" wire:click="sendVerification" class="button-primary">
                {{ __('Renvoyer un code de vérification') }}
            </button>

            <!-- Logout link -->
            <button type="button" wire:click="logout" class="button-secondary">
                {{ __('Se déconnecter') }}
            </button>
        </div>
    </div>
</x-auth-template>

