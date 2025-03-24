<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl-semibold text-gray-900 dark:text-gray-100 mb-1">{{ __('Invitation expirée') }}</h2>
            <p class="text-sm-regular text-gray-500 dark:text-gray-400 mb-6">{{ __('Cette invitation n\'est plus valide') }}</p>

            <div class="flex items-center p-4 mb-6 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-800">
                <x-svg.error class="w-5 h-5 text-red-500 dark:text-red-400 mr-3 flex-shrink-0" />
                <div class="text-sm-regular text-red-700 dark:text-red-300">
                    <p>Cette invitation n'est plus valide ou a expiré.</p>
                    <p class="mt-1">Veuillez contacter le membre de la famille qui vous a invité pour recevoir une nouvelle invitation.</p>
                </div>
            </div>

            <div class="pt-4">
                <a href="{{ route('welcome') }}" class="button-secondary w-full flex items-center justify-center">
                    <x-svg.home class="text-indigo-700 mr-2" />
                    {{ __('Retour à l\'accueil') }}
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
