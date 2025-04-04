<div>
    @if ($spotlightOpen)
        <div class="fixed z-80 inset-0 overflow-y-auto"
             aria-labelledby="Barre de recherche"
             role="dialog"
             aria-modal="true"
             @keydown.escape.window="$wire.set('spotlightOpen', false)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
        >
            <div class="flex justify-center min-h-screen" tabindex="-1">
                {{-- Overlay --}}
                <div aria-hidden="true"
                     wire:click="$set('spotlightOpen', false)"
                     class="fixed inset-0 bg-gray-900 opacity-20 transition-opacity dark:bg-black dark:opacity-30"
                ></div>

                {{-- Content --}}
                <div class="absolute top-18 md:top-[20vh] max-sm:w-[calc(100vw-2rem)] transform transition-all ">
                    <div
                        class="bg-gray-100 dark:bg-gray-900 rounded-lg md:rounded-xl md:max-w-[40rem] max-h-[40rem] border border-gray-300 dark:border-gray-700 overflow-hidden">

                        {{-- Search bar --}}
                        <div class="relative bg-white dark:bg-gray-800">
                            <label for="search">
                                <span class="sr-only">Barre de recherche</span>
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex-center pl-5">
                                    <x-svg.search class="h-4 w-4 text-gray-400 dark:text-gray-500"/>
                                </div>
                            </label>
                            <input x-ref="searchInput"
                                   type="text"
                                   id="search"
                                   aria-label="Search"
                                   placeholder="Rechercher..."
                                   wire:model.live.debounce.200ms="search"
                                   x-init="$wire.spotlightOpen && setTimeout(() => $refs.searchInput.focus(), 200)"
                                   class="block w-full py-3 pl-13 pr-3 text-md-regular text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-0 focus:border-gray-300 dark:focus:border-gray-600 dark:bg-gray-800 dark:border-gray-800"
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 max-sm:hidden">
                                <x-shortcut key="⌘ K" class="text-gray-500 dark:text-gray-400"/>
                            </div>
                        </div>

                        {{-- Scrollable div --}}
                        <div class="overflow-y-scroll max-h-[24.5rem]">
                            {{-- État initial : aucune recherche commencée --}}
                            @if(empty($search))
                                <div
                                    class="flex flex-col items-center justify-center h-48 bg-gray-100 border-t border-slate-200">
                                    <x-svg.search-classic class="h-10 w-10 text-gray-500 dark:text-gray-600 mb-5"/>
                                    <p class="text-sm-medium text-gray-500 dark:text-gray-400">
                                        Aucune recherche commencée
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-500 mt-2">
                                        Commencez à taper pour rechercher...
                                    </p>
                                </div>
                                {{-- Recherche avec résultats --}}
                            @elseif($results->isNotEmpty())
                                <ul>
                                    @foreach($results as $section => $items)
                                        <li wire:key="{{ $section }}">
                                            <x-divider class="dark:bg-gray-700"/>

                                            <div class="bg-white dark:bg-gray-800 py-3 px-2">
                                                <h3 class="text-sm-medium text-gray-500 dark:text-gray-400 mb-2 pl-2.5">
                                                    {{ $section }}
                                                </h3>
                                                <ul role="list" class="flex flex-col gap-1">
                                                    @foreach ($items as $result)
                                                        @if ($result instanceof \App\Models\Invoice)
                                                            <x-spotlight.result
                                                                wirekey="{{ $result->id }}"
                                                                href="{{ route('invoices.show', $result) }}"
                                                                text="{{ $result->name }}"
                                                                description="({{ $result->amount }} {{ $result->currency }})"
                                                                state="{{ $result->is_archived ? '#archivée' : null }}"
                                                            >
                                                                <x-svg.invoice
                                                                    class="h-5 w-5 group-hover:text-gray-800"/>
                                                            </x-spotlight.result>
                                                        @endif

                                                        @if ($result instanceof \App\Models\User)
                                                            <x-spotlight.result
                                                                wirekey="{{ $result->id }}"
                                                                href="{{ route('settings.profile') }}"
                                                                text="{{ $result->name }}"
                                                                description="{{ $result->email }}"
                                                                state="{{ $result->getFamilyPermissionAttribute() ?? null }}"
                                                            >
                                                                <img
                                                                    class="h-6 w-6 rounded-full bg-gray-100 dark:bg-gray-700"
                                                                    src="{{ $result->avatar_url ?? asset('img/avatar_placeholder.png') }}"
                                                                    alt="{{ $result->name }}"/>
                                                            </x-spotlight.result>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @elseif($results->isEmpty())
                                {{-- Recherche sans résultats --}}
                                <div class="flex items-center justify-start gap-3 py-3 px-4.5  border-slate-200">
                                    <p class="text-sm-medium text-gray-500 dark:text-gray-400">
                                        Aucun résultat pour "{{ $search }}"
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-500">
                                        Essayez d'autres mots-clés ou vérifiez l'orthographe.
                                    </p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 py-3 px-2">
                                    <h3 class="text-sm-medium text-gray-500 dark:text-gray-400 mb-2 pl-2.5">
                                        Suggestions
                                    </h3>
                                    <ul role="list" class="flex flex-col gap-1">
                                        <x-spotlight.result href="{{ route('invoices.create') }}"
                                                            text="Ajouter une nouvelle facture" shortcut="⌘ X">
                                            <x-svg.add2
                                                class="h-4 w-4 text-gray-400 dark:text-gray-500 group-hover:text-gray-800"/>
                                        </x-spotlight.result>
                                        <x-spotlight.result href="{{ route('invoices.archived') }}"
                                                            text="Voir mes factures archivées">
                                            <x-svg.archive
                                                class="h-4 w-4 text-gray-400 dark:text-gray-500 group-hover:text-gray-800"/>
                                        </x-spotlight.result>
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <x-divider/>

                        {{-- Bottom navigation --}}
                        <div class="bg-white dark:bg-gray-800 pl-4.5 pr-3 py-1.5 min-w-[40rem] max-sm:hidden">
                            <div class="flex items-center justify-between space-x-4">

                                {{-- Arrows up/down --}}
                                <div class="flex-center space-x-2">
                                    <div
                                        class="py-2 px-2 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-700">
                                        <svg class="w-2.5 h-2.5 text-gray-500 dark:text-gray-400" width="12" height="12"
                                             viewBox="0 0 12 12" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M6.00004 10.6667V1.33337M6.00004 1.33337L1.33337 6.00004M6.00004 1.33337L10.6667 6.00004"
                                                stroke="currentColor" stroke-width="1.67" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div
                                        class="py-2 px-2 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-700">
                                        <svg class="w-2.5 h-2.5 text-gray-500 dark:text-gray-400" width="12" height="12"
                                             viewBox="0 0 12 12" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M6.00004 1.33337V10.6667M6.00004 10.6667L10.6667 6.00004M6.00004 10.6667L1.33337 6.00004"
                                                stroke="currentColor" stroke-width="1.67" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm-medium text-gray-500 dark:text-gray-400">naviguer</span>
                                </div>

                                {{-- Arrow enter --}}
                                <div class="flex-center space-x-2">
                                    <div
                                        class="py-2 px-2 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-700">
                                        <svg class="w-2.5 h-2.5 text-gray-500 dark:text-gray-400" width="14" height="14"
                                             viewBox="0 0 14 14" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M12.3334 1.66663V2.59996C12.3334 4.84017 12.3334 5.96027 11.8974 6.81592C11.5139 7.56857 10.902 8.18049 10.1493 8.56398C9.29367 8.99996 8.17356 8.99996 5.93335 8.99996H1.66669M1.66669 8.99996L5.00002 5.66663M1.66669 8.99996L5.00002 12.3333"
                                                stroke="currentColor" stroke-width="1.67" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm-medium text-gray-500 dark:text-gray-400">sélectionné</span>
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
                                    <a href="{{ route('settings.index') }}" class="inline-block py-2.5 px-2"
                                       title="Vers les paramètres" wire:navigate>
                                        <x-svg.settings class="text-gray-500 hover:text-gray-800"/>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Close spotlight --}}
                {{--<button type="button" wire:click="$set('spotlightOpen', false)" class="absolute top-4 right-4 p-1.5 rounded-md bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600">
                    <x-svg.cross class="h-4 w-4" />
                </button>--}}
            </div>
        </div>
    @endif
</div>

@script
<script>
    // Keyboard shortcuts handler
    document.addEventListener('keydown', function (event) {
        // Si touche ⌘ ou CTRL est pressée
        if (event.metaKey || event.ctrlKey) {
            // Raccourci ⌘+K ou CTRL+K pour le spotlight
            if (event.key === 'k') {
                event.preventDefault();
                @this.
                set('spotlightOpen', !@this.spotlightOpen)
            }
            // Raccourci ⌘+X ou CTRL+X pour ajouter une facture
            else if (event.key === 'x') {
                event.preventDefault();
                document.getElementById('add-invoice-link')?.click();
            }
        }
    });
</script>
@endscript
