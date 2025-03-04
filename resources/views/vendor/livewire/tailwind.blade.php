@php
    if (! isset($scrollTo)) {
        $scrollTo = 'body';
    }

    $scrollIntoViewJsSnippet = ($scrollTo !== false)
        ? <<<JS
           (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView({behavior: 'smooth'})
        JS
        : '';
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex-center gap-2 p-4">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="flex-center gap-1.5 px-3 py-2 text-gray-400 rounded-lg cursor-not-allowed select-none">
                <x-svg.arrows.left class="w-4 h-4"/>
                <span class="hidden sm:inline text-sm-medium">{{ __('Précédent') }}</span>
            </span>
        @else
            <button
                wire:click="previousPage('{{ $paginator->getPageName() }}')"
                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                wire:loading.attr="disabled"
                class="flex-center gap-1.5 px-3 py-2 text-gray-600 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition duration-150 ease-in-out"
            >
                <x-svg.arrows.left class="w-4 h-4"/>
                <span class="hidden sm:inline text-sm-medium">{{ __('Précédent') }}</span>
            </button>
        @endif

        {{-- Pagination Elements --}}
        <div class="hidden sm:flex-center gap-1">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true" class="flex-center w-8 h-8 text-sm-medium text-gray-400">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                            @if ($page == $paginator->currentPage())
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
                @endif
            @endforeach
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
                class="flex-center gap-1.5 px-3 py-2 text-sm-medium text-gray-600 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-400  hover:text-gray-900 transition duration-150 ease-in-out"
            >
                <span class="hidden sm:inline text-sm-medium">{{ __('Suivant') }}</span>
                <x-svg.arrows.right class="w-4 h-4"/>
            </button>
        @else
            <span class="flex-center gap-1.5 px-3 py-2 text-gray-400 rounded-lg cursor-not-allowed select-none">
                <span class="hidden sm:inline text-sm-medium">{{ __('Suivant') }}</span>
                <x-svg.arrows.right class="w-4 h-4"/>
            </span>
        @endif
    </nav>
@endif
