<div>
    <div class="max-w-md mx-auto flex-center h-screen">
        <div class="p-6 h-fit bg-white dark:bg-gray-800 rounded-2xl overflow-hidden">

            <h1 role="heading" aria-level="1" class="text-xl-semibold text-gray-900 dark:text-gray-100 mb-1">
                {{ __('Rejoindre la famille') }}
            </h1>

            <p class="text-sm-regular text-gray-500 dark:text-gray-400 mb-6">
                {{ __('Vous pouvez accepter l\'invitation à rejoindre cette famille') }}
            </p>

            <div class="flex items-center p-4 mb-6 rounded-xl bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800">
                <x-svg.info class="w-5 h-5 text-blue-500 dark:text-blue-400 mr-3 flex-shrink-0"/>
                <div class="text-sm-regular text-blue-700 dark:text-blue-300">
                    <p>
                        Bonjour <span class="font-medium">{{ $user->name }}</span>, vous avez été invité à rejoindre la
                        famille <span class="font-medium">{{ $invitation->family->name }}</span> en tant
                        que {{ $invitation->relation }}.
                    </p>
                </div>
            </div>

            <x-flash-messages />

            <button wire:click="acceptInvitation" class="button-tertiary w-full">
                <x-svg.valid class="text-white"/>
                {{ __('Accepter l\'invitation') }}
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
