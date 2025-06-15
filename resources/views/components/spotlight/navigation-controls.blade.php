<div class="bg-white dark:bg-gray-800 pl-4.5 pr-3 py-1.5 md:min-w-[40rem] max-sm:hidden">
    <div class="flex items-center justify-between space-x-4">
        {{-- Arrows up/down --}}
        <div class="flex-center space-x-2">
            <div class="py-2 px-2 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-700">
                <svg class="w-2.5 h-2.5 text-gray-500 dark:text-gray-400" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.00004 10.6667V1.33337M6.00004 1.33337L1.33337 6.00004M6.00004 1.33337L10.6667 6.00004" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="py-2 px-2 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-700">
                <svg class="w-2.5 h-2.5 text-gray-500 dark:text-gray-400" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.00004 1.33337V10.6667M6.00004 10.6667L10.6667 6.00004M6.00004 10.6667L1.33337 6.00004" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <span class="text-sm-medium text-gray-500 dark:text-gray-400">naviguer</span>
        </div>

        {{-- Arrow enter --}}
        <div class="flex-center space-x-2">
            <div class="py-2 px-2 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-700">
                <svg class="w-2.5 h-2.5 text-gray-500 dark:text-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.3334 1.66663V2.59996C12.3334 4.84017 12.3334 5.96027 11.8974 6.81592C11.5139 7.56857 10.902 8.18049 10.1493 8.56398C9.29367 8.99996 8.17356 8.99996 5.93335 8.99996H1.66669M1.66669 8.99996L5.00002 5.66663M1.66669 8.99996L5.00002 12.3333" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <span class="text-sm-medium text-gray-500 dark:text-gray-400">sélectionner</span>
        </div>

        {{-- Key escape --}}
        <div class="flex-center space-x-2">
            <div class="pt-1 pb-1 px-1.5 rounded-lg border border-gray-300 dark:border-gray-700 text-sm-medium text-gray-500 dark:text-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600">
                esc
            </div>
            <span class="text-sm-medium text-gray-500 dark:text-gray-400">fermer</span>
        </div>

        {{-- Settings --}}
        <div class="ml-auto flex">
            <a href="{{ route('settings.index') }}" class="inline-block py-2.5 px-2" title="Vers les paramètres" wire:navigate>
                <x-svg.settings class="text-gray-500 hover:text-gray-800"/>
            </a>
        </div>
    </div>
</div>
