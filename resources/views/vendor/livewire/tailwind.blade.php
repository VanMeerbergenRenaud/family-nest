@php
    if (! isset($scrollTo)) {
        $scrollTo = 'table';
    }

    $scrollIntoViewJsSnippet = ($scrollTo !== false)
        ? <<<JS
           (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView({behavior: 'smooth'})
        JS
        : '';

    $windowSize = 2; // Number of pages to show on each side of the current page
    $showDotsLimit = 8; // Maximum number of pages to show before using dots
    $perPageOptions = [8, 15, 25, 50, 100]; // Number of rows display options
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-4 px-4 py-3">
        {{-- Résultats et sélecteur par page (gauche) --}}
        <div class="flex flex-row max-sm:justify-between flex-wrap items-center gap-4 w-full sm:w-auto">

            <p class="text-sm text-gray-700 dark:text-gray-400">
                {{ __('Résultats') }}
                <span class="text-sm-medium">{{ $paginator->firstItem() }}</span>
                {{ __('à') }}
                <span class="text-sm-medium">{{ $paginator->lastItem() }}</span>
                {{ __('sur') }}
                <span class="text-sm-medium">{{ $paginator->total() }}</span>
            </p>

            <div class="flex items-center gap-2">
                <label for="per-page" class="text-sm text-gray-600 dark:text-gray-400">{{ __('Par page') }}:</label>
                <select
                    id="per-page"
                    wire:model.live="perPage"
                    x-on:change="{{ $scrollIntoViewJsSnippet }}"
                    class="border border-gray-300 dark:border-gray-600 rounded-md text-sm py-1 px-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 cursor-pointer focus:ring focus:ring-blue-300 focus:border-blue-300 dark:focus:border-blue-700"
                >
                    @foreach ($perPageOptions as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Pagination (droite) --}}
        <div class="flex items-center max-sm:justify-between justify-end w-full sm:w-auto">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="flex items-center gap-1.5 px-3 py-2 text-gray-400 rounded-lg cursor-not-allowed select-none">
                    <x-svg.arrows.left class="text-gray-400" />
                    <span class="hidden sm:inline text-sm-medium">{{ __('Précédent') }}</span>
                </span>
            @else
                <button
                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                    wire:loading.attr="disabled"
                    class="flex items-center gap-1.5 px-3 py-2 text-gray-600 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-700 dark:hover:text-gray-200 transition duration-150 ease-in-out"
                >
                    <x-svg.arrows.left />
                    <span class="hidden sm:inline text-sm-medium">{{ __('Précédent') }}</span>
                </button>
            @endif

            {{-- Pagination Elements --}}
            <div class="hidden sm:flex items-center gap-1 mx-2">
                @php
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                @endphp

                {{-- Add all pages if the total is less than or equal to the limit --}}
                @if ($lastPage <= $showDotsLimit)
                    @foreach (range(1, $lastPage) as $page)
                        <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                            @if ($page == $currentPage)
                                <span aria-current="page" class="flex items-center justify-center w-8 h-8 text-sm-medium text-white bg-gray-800 dark:bg-gray-600 rounded-lg">
                                    {{ $page }}
                                </span>
                            @else
                                <button
                                    wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    class="flex items-center justify-center w-8 h-8 text-sm-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg transition-colors duration-150"
                                    aria-label="{{ __('Aller à la page :page', ['page' => $page]) }}"
                                >
                                    {{ $page }}
                                </button>
                            @endif
                        </span>
                    @endforeach
                @else
                    {{-- First page --}}
                    <span wire:key="paginator-{{ $paginator->getPageName() }}-page1">
                        <button
                            wire:click="gotoPage(1, '{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            class="flex items-center justify-center w-8 h-8 text-sm-medium {{ $currentPage == 1 ? 'text-white bg-gray-800 dark:bg-gray-600 rounded-lg' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg transition-colors duration-150' }}"
                            aria-label="{{ __('Aller à la page 1') }}"
                        >
                            1
                        </button>
                    </span>

                    {{-- Dots --}}
                    @if ($currentPage > $windowSize + 2)
                        <span aria-disabled="true" class="flex items-center justify-center w-8 h-8 text-sm-medium text-gray-400">
                            ...
                        </span>
                    @endif

                    {{-- Pages until the current page --}}
                    @foreach (range(max(2, $currentPage - $windowSize), min($lastPage - 1, $currentPage + $windowSize)) as $page)
                        <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                            @if ($page == $currentPage)
                                <span aria-current="page"
                                      class="flex items-center justify-center w-8 h-8 text-sm-medium text-white bg-gray-800 dark:bg-gray-600 rounded-lg">
                                    {{ $page }}
                                </span>
                            @else
                                <button
                                    wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    class="flex items-center justify-center w-8 h-8 text-sm-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg transition-colors duration-150"
                                    aria-label="{{ __('Aller à la page :page', ['page' => $page]) }}"
                                >
                                    {{ $page }}
                                </button>
                            @endif
                        </span>
                    @endforeach

                    {{-- Dots --}}
                    @if ($currentPage < $lastPage - $windowSize - 1)
                        <span aria-disabled="true" class="flex items-center justify-center w-8 h-8 text-sm-medium text-gray-400">
                            ...
                        </span>
                    @endif

                    {{-- Last page --}}
                    @if ($lastPage > 1)
                        <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $lastPage }}">
                            <button
                                wire:click="gotoPage({{ $lastPage }}, '{{ $paginator->getPageName() }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                class="flex items-center justify-center w-8 h-8 text-sm-medium {{ $currentPage == $lastPage ? 'text-white bg-gray-800 dark:bg-gray-600 rounded-lg' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg transition-colors duration-150' }}"
                                aria-label="{{ __('Aller à la page :page', ['page' => $lastPage]) }}"
                            >
                                {{ $lastPage }}
                            </button>
                        </span>
                    @endif
                @endif
            </div>

            {{-- Current Page Indicator (Mobile) --}}
            <span class="inline sm:hidden px-3 py-1 text-sm-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button
                    wire:click="nextPage('{{ $paginator->getPageName() }}')"
                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                    wire:loading.attr="disabled"
                    class="flex items-center gap-1.5 px-3 py-2 text-gray-600 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-700 dark:hover:text-gray-200 transition duration-150 ease-in-out"
                >
                    <span class="hidden sm:inline text-sm-medium">{{ __('Suivant') }}</span>
                    <x-svg.arrows.right />
                </button>
            @else
                <span class="flex items-center gap-1.5 px-3 py-2 text-gray-400 rounded-lg cursor-not-allowed select-none">
                    <span class="hidden sm:inline text-sm-medium">{{ __('Suivant') }}</span>
                    <x-svg.arrows.right class="text-gray-400" />
                </span>
            @endif
        </div>
    </nav>
@endif
