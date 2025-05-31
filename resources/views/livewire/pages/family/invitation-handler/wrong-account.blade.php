<div>
    <div class="max-w-md mx-auto flex-center h-screen">
        <div class="p-6 h-fit bg-white dark:bg-gray-800 rounded-2xl overflow-hidden">

            <h1 role="heading" aria-level="1" class="text-xl-semibold text-gray-900 dark:text-gray-100 mb-1">
                {{ __('Compte incorrect') }}
            </h1>

            <p class="text-sm-regular text-gray-500 dark:text-gray-400 mb-6">
                {{ __('Vous n\'utilisez pas le bon compte') }}
            </p>

            <x-flash-messages/>

            <div class="flex items-center p-4 mb-6 rounded-xl bg-amber-50 dark:bg-amber-900/30 border border-amber-100 dark:border-amber-800">
                <x-svg.warning class="w-5 h-5 text-amber-500 dark:text-amber-400 mr-3 flex-shrink-0"/>
                <div class="text-sm-regular text-amber-700 dark:text-amber-300">
                    <p>
                        Cette invitation a été envoyée à l'adresse email
                        <span class="font-medium">{{ $invitation->email }}</span>, mais vous êtes actuellement connecté
                        avec un compte différent.
                    </p>
                    <p class="mt-1">
                        Veuillez vous déconnecter et vous connecter avec le compte associé à cette adresse email, ou
                        créer un nouveau compte.
                    </p>
                </div>
            </div>

            <button wire:click="logout" class="button-tertiary w-full flex-center">
                <x-svg.logout class="text-white"/>
                {{ __('Se déconnecter') }}
            </button>

            <div class="mt-4 flex justify-center">
                <a href="{{ route('welcome') }}"
                   class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                    {{ __('Retour à l\'accueil') }}
                </a>
            </div>
        </div>
    </div>
</div>
