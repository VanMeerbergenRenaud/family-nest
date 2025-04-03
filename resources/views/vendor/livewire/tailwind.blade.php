@php
    if (! isset($scrollTo)) {
        $scrollTo = 'table';
    }

    $scrollIntoViewJsSnippet = ($scrollTo !== false)
        ? <<<JS
           (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView({behavior: 'smooth'})
        JS
        : '';

    // Limit pagination
    $windowSize = 2; // Number of pages to show on each side of the current page
    $showDotsLimit = 8; // Maximum number of pages to show before using dots
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex-center gap-2 p-4">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="flex-center gap-1.5 px-3 py-2 text-gray-400 rounded-lg cursor-not-allowed select-none">
                <x-svg.arrows.left/>
                <span class="hidden sm:inline text-sm-medium">{{ __('Précédent') }}</span>
            </span>
        @else
            <button
                wire:click="previousPage('{{ $paginator->getPageName() }}')"
                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                wire:loading.attr="disabled"
                class="flex-center gap-1.5 px-3 py-2 text-gray-600 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition duration-150 ease-in-out"
            >
                <x-svg.arrows.left/>
                <span class="hidden sm:inline text-sm-medium">{{ __('Précédent') }}</span>
            </button>
        @endif

        {{-- Pagination Elements --}}
        <div class="hidden sm:flex-center gap-1">
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
            @endphp

            {{-- Add all pages if the total is less than or equal to the limit --}}
            @if ($lastPage <= $showDotsLimit)
                @foreach (range(1, $lastPage) as $page)
                    <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                        @if ($page == $currentPage)
                            <span aria-current="page"
                                  class="flex-center w-8 h-8 text-sm-medium text-white bg-gray-800 rounded-lg font-medium">
                                {{ $page }}
                            </span>
                        @else
                            <button
                                wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                class="flex-center w-8 h-8 text-sm-medium text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-400 hover:text-gray-900 rounded-lg transition-colors duration-150"
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
                        class="flex-center w-8 h-8 text-sm-medium {{ $currentPage == 1 ? 'text-white bg-gray-800 rounded-lg font-medium' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-400 hover:text-gray-900 rounded-lg transition-colors duration-150' }}"
                        aria-label="{{ __('Aller à la page 1') }}"
                    >
                        1
                    </button>
                </span>

                {{-- Dots --}}
                @if ($currentPage > $windowSize + 2)
                    <span aria-disabled="true" class="flex-center w-8 h-8 text-sm-medium text-gray-400">
                        ...
                    </span>
                @endif

                {{-- Pages until the current page --}}
                @foreach (range(max(2, $currentPage - $windowSize), min($lastPage - 1, $currentPage + $windowSize)) as $page)
                    <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                        @if ($page == $currentPage)
                            <span aria-current="page"
                                  class="flex-center w-8 h-8 text-sm-medium text-white bg-gray-800 rounded-lg font-medium">
                                {{ $page }}
                            </span>
                        @else
                            <button
                                wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                class="flex-center w-8 h-8 text-sm-medium text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-400 hover:text-gray-900 rounded-lg transition-colors duration-150"
                                aria-label="{{ __('Aller à la page :page', ['page' => $page]) }}"
                            >
                                {{ $page }}
                            </button>
                        @endif
                    </span>
                @endforeach

                {{-- Dots --}}
                @if ($currentPage < $lastPage - $windowSize - 1)
                    <span aria-disabled="true" class="flex-center w-8 h-8 text-sm-medium text-gray-400">
                        ...
                    </span>
                @endif

                {{-- Last page --}}
                @if ($lastPage > 1)
                    <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $lastPage }}">
                        <button
                            wire:click="gotoPage({{ $lastPage }}, '{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            class="flex-center w-8 h-8 text-sm-medium {{ $currentPage == $lastPage ? 'text-white bg-gray-800 rounded-lg font-medium' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-400 hover:text-gray-900 rounded-lg transition-colors duration-150' }}"
                            aria-label="{{ __('Aller à la page :page', ['page' => $lastPage]) }}"
                        >
                            {{ $lastPage }}
                        </button>
                    </span>
                @endif
            @endif
        </div>

        {{-- Current Page Indicator (Mobile) --}}
        <span class="inline sm:hidden px-3 py-1 text-sm-medium text-gray-600 bg-gray-100 rounded">
            {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </span>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <button
                wire:click="nextPage('{{ $paginator->getPageName() }}')"
                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                wire:loading.attr="disabled"
                class="flex-center gap-1.5 px-3 py-2 text-gray-600 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition duration-150 ease-in-out"
            >
                <span class="hidden sm:inline text-sm-medium">{{ __('Suivant') }}</span>
                <x-svg.arrows.right/>
            </button>
        @else
            <span class="flex-center gap-1.5 px-3 py-2 text-gray-400 rounded-lg cursor-not-allowed select-none">
                <span class="hidden sm:inline text-sm-medium">{{ __('Suivant') }}</span>
                <x-svg.arrows.right/>
            </span>
        @endif
    </nav>
@endif
