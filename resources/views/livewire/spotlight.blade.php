<div>
    @if ($spotlightOpen)
        <div class="fixed z-50 inset-0 overflow-y-auto"
             aria-labelledby="spotlight-modal"
             role="dialog"
             aria-modal="true"
             @keydown.escape.window="$wire.set('spotlightOpen', false)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
        >
            <div class="flex justify-center min-h-screen">
                <div class="fixed inset-0 bg-gray-900 opacity-20 transition-opacity"
                     aria-hidden="true" {{--wire:click="$set('spotlightOpen', false)"--}}></div>
                <div class="relative top-[20vh] rounded-xl transform transition-all ">

                    <div
                        class="bg-gray-100 rounded-xl max-w-[40rem] max-h-[40rem] border border-gray-300 overflow-hidden">

                        {{-- Search bar --}}
                        <div class="relative w-full bg-white">
                            <label for="search">
                                <span class="sr-only">Barre de recherche</span>
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex-center pl-5">
                                    <x-svg.search class="h-4 w-4 text-gray-400"/>
                                </div>
                            </label>
                            <input autofocus
                                   type="text"
                                   id="search"
                                   aria-label="Search"
                                   placeholder="Rechercher..."
                                   wire:model.live.debounce.300ms="search"
                                   class="block w-full py-4 pl-12 pr-3 text-md-regular text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-0 focus:border-gray-300"
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                <kbd
                                    class="pointer-events-none flex-center gap-1.5 rounded-md border-gray bg-gray-50 px-2 py-0.5 font-sans text-xs-medium text-gray-500">
                                    ⌘&nbsp;K
                                </kbd>
                            </div>
                        </div>

                        {{-- Test empty search --}}
                        @if(!empty($search))
                            <x-divider/>
                            <div class="bg-white py-3 px-2">
                                <h3 class="text-sm-medium text-gray-500 mb-2 pl-2.5">
                                    Suggestions
                                </h3>
                                <ul role="list" class="flex flex-col gap-1">
                                    <x-spotlight-result href="#" text="Voir mes factures">
                                        <x-svg.changelog class="h-4 w-4 text-gray-400"/>
                                    </x-spotlight-result>
                                    <x-spotlight-result href="#" text="Ajouter une nouvelle facture" shortcut="⌘ N">
                                        <x-svg.help class="h-4 w-4 text-gray-400"/>
                                    </x-spotlight-result>
                                    <x-spotlight-result href="#" text="Inviter un membre à la famille">
                                        <x-svg.user-plus class="h-4 w-4 text-gray-400"/>
                                    </x-spotlight-result>
                                </ul>
                            </div>
                        @else
                            <p class="text-sm-medium text-gray-500 pl-3.5 py-3">
                                Aucun résultat pour l'instant
                            </p>
                        @endif

                        {{-- Results --}}
                        @if (!empty($results))
                            <x-divider/>

                            @foreach($results as $section => $items)
                                {{-- On boucle sur les sections de résultats (Utilisateurs, Produits, etc.) --}}
                                <div class="bg-white py-3 px-2">
                                    <h3 class="text-sm-medium text-gray-500 mb-2 pl-2.5">
                                        {{ $section }}
                                    </h3>
                                    <ul role="list" class="flex flex-col gap-1">
                                        @foreach ($items as $result)
                                            {{-- On boucle sur les résultats DANS chaque section --}}
                                            @if ($result instanceof \App\Models\User)
                                                {{-- Condition pour afficher différemment selon le modèle --}}
                                                <x-spotlight-result href="#" text="{{ $result->name }}"
                                                                    description="({{ $result->email }})" shortcut="moi">
                                                    <x-svg.user class="h-5 w-5 text-gray-400"/>
                                                </x-spotlight-result>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm-medium text-gray-500 pl-3.5 py-3">
                                Aucun résultat pour "{{ $search }}"
                            </p>
                        @endif

                        <x-divider/>

                        {{-- Bottom navigation --}}
                        <div class="bg-white pl-4.5 pr-3 py-1.5 min-w-[40rem]">
                            <div class="flex items-center justify-between space-x-4">

                                {{-- Arrows up/down --}}
                                <div class="flex-center space-x-2">
                                    <button type="button" class="py-2 px-1.5 rounded-lg border border-gray-300">
                                        <svg class="w-2.5 h-2.5" width="12" height="12" viewBox="0 0 12 12" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M6.00004 10.6667V1.33337M6.00004 1.33337L1.33337 6.00004M6.00004 1.33337L10.6667 6.00004"
                                                stroke="#667085" stroke-width="1.67" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                    <button type="button" class="py-2 px-1.5 rounded-lg border border-gray-300">
                                        <svg class="w-2.5 h-2.5" width="12" height="12" viewBox="0 0 12 12" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M6.00004 1.33337V10.6667M6.00004 10.6667L10.6667 6.00004M6.00004 10.6667L1.33337 6.00004"
                                                stroke="#667085" stroke-width="1.67" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                    <span class="text-sm-medium text-gray-500">naviguer</span>
                                </div>

                                {{-- Arrow enter --}}
                                <div class="flex-center space-x-2">
                                    <button type="button" class="py-2 px-1.5 rounded-lg border border-gray-300">
                                        <svg class="w-2.5 h-2.5" width="14" height="14" viewBox="0 0 14 14" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M12.3334 1.66663V2.59996C12.3334 4.84017 12.3334 5.96027 11.8974 6.81592C11.5139 7.56857 10.902 8.18049 10.1493 8.56398C9.29367 8.99996 8.17356 8.99996 5.93335 8.99996H1.66669M1.66669 8.99996L5.00002 5.66663M1.66669 8.99996L5.00002 12.3333"
                                                stroke="#667085" stroke-width="1.67" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                    <span class="text-sm-medium text-gray-500">sélectionné</span>
                                </div>

                                {{-- Key escape --}}
                                <div class="flex-center space-x-2">
                                    <button type="button"
                                            class="pt-1 pb-1 px-1.5 rounded-lg border border-gray-300 text-sm-medium text-gray-500">
                                        esc
                                    </button>
                                    <span class="text-sm-medium text-gray-500">fermer</span>
                                </div>

                                {{-- Settings --}}
                                <div class="ml-auto">
                                    <button type="button" class="py-2.5 px-2">
                                        <x-svg.settings class="h-4 w-4 text-gray-400"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--<button wire:click="$set('spotlightOpen', false)" type="button">
                        Fermer
                    </button>--}}
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    // Spotlight keyboard ⌘+k shortcut
    document.addEventListener('keydown', function (event) {
        if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
            event.preventDefault();
            @this.set('spotlightOpen', !@this.spotlightOpen)
        }
    });
</script>
