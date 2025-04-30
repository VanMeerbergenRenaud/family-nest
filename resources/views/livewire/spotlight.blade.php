<div>
    @if ($spotlightOpen)
        <div class="fixed z-80 inset-0 overflow-y-auto"
             aria-labelledby="Barre de recherche"
             role="dialog"
             aria-modal="true"
             @keydown.escape.window="$wire.set('spotlightOpen', false)"
             @keydown.up.prevent.window="$wire.navigateUp()"
             @keydown.down.prevent.window="$wire.navigateDown()"
             @keydown.enter.prevent.window="$wire.selectCurrent()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="flex justify-center min-h-screen" tabindex="-1">
                {{-- Overlay --}}
                <div aria-hidden="true"
                     wire:click="$set('spotlightOpen', false)"
                     class="fixed inset-0 bg-gray-900 opacity-20 transition-opacity dark:bg-black dark:opacity-30">
                </div>

                {{-- Content --}}
                <div class="absolute top-18 md:top-[20vh] max-sm:w-[calc(100vw-2rem)] transform transition-all">
                    <div class="bg-gray-100 dark:bg-gray-900 rounded-lg md:rounded-xl md:max-w-[40rem] max-h-[40rem] border border-gray-300 dark:border-gray-700 overflow-hidden">
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
                                   class="block w-full py-3 pl-13 pr-3 lg:pr-18 text-md-regular text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-0 focus:border-gray-300 dark:focus:border-gray-600 dark:bg-gray-800 dark:border-gray-800"/>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 max-sm:hidden">
                                <x-shortcut key="⌘ K" class="text-gray-500 dark:text-gray-400"/>
                            </div>
                        </div>

                        {{-- Indicator du mode avancé --}}
                        @if($showAdvancedSearch && !empty($search))
                            <div class="bg-slate-100 px-4 py-2">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <p class="text-sm text-gray-700">
                                        <span class="text-sm-medium">Mode avancé</span> - Affichage de tous les résultats ({{ $totalResultsCount }})
                                    </p>
                                    <button
                                        type="button"
                                        wire:click="toggleAdvancedSearch"
                                        class="underline text-gray-700 text-sm hover:text-gray-800"
                                    >
                                        Mode standard
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- Results content --}}
                        <div class="overflow-y-scroll max-h-[24.5rem]" id="spotlight-results">
                            @if(empty($search))
                                <x-spotlight.empty-state />
                            @elseif($results->isNotEmpty())
                                <x-spotlight.results-list :results="$results" />

                                {{-- Bouton "Voir plus" --}}
                                @if($hasMoreResults)
                                    <x-divider class="dark:bg-gray-700"/>

                                    <div class="bg-white dark:bg-gray-800 p-2">
                                        <button type="button"
                                                wire:click="toggleAdvancedSearch"
                                                id="show-more-button"
                                                wire:key="show-more"
                                                class="button-classic w-full text-blue-600 justify-center hover:bg-gray-100"
                                        >
                                            <x-svg.search-classic class="text-blue-600" />
                                            Voir tous les résultats ({{ $totalResultsCount }})
                                        </button>
                                    </div>
                                @endif
                            @else
                                <x-spotlight.no-results :search="$search" />
                            @endif
                        </div>

                        <x-divider/>

                        {{-- Navigation controls --}}
                        <x-spotlight.navigation-controls />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    // Keyboard shortcuts
    document.addEventListener('keydown', function (event) {
        // Open spotlight with CMD+K / CTRL+K
        if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
            event.preventDefault();
            @this.set('spotlightOpen', !@this.spotlightOpen)
        }

        // Add invoice with CMD+X / CTRL+X
        if ((event.metaKey || event.ctrlKey) && event.key === 'x') {
            event.preventDefault();
            document.getElementById('add-invoice-link')?.click();
        }
    });

    // Selected item scrolling
    document.addEventListener('livewire:initialized', function() {
        Livewire.hook('commit', ({ component, succeed }) => {
            succeed(() => {
                if (component.name === 'spotlight') {
                    requestAnimationFrame(() => {
                        let selectedItem = document.getElementById('selected-item');
                        const resultsContainer = document.getElementById('spotlight-results');

                        // Aussi considérer le bouton "Voir plus" comme un élément navigable
                        const showMoreButton = document.getElementById('show-more-button');
                        if (showMoreButton && showMoreButton.classList.contains('bg-gray-100')) {
                            selectedItem = showMoreButton;
                        }

                        if (selectedItem && resultsContainer) {
                            const containerRect = resultsContainer.getBoundingClientRect();
                            const selectedRect = selectedItem.getBoundingClientRect();

                            // Check if selected item is visible
                            const isVisible = (
                                selectedRect.top >= containerRect.top &&
                                selectedRect.bottom <= containerRect.bottom
                            );

                            // Scroll to selected item if not visible
                            if (!isVisible) {
                                selectedItem.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'nearest'
                                });
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endscript
