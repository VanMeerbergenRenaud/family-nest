<div class="relative p-4">
    <nav class="lg:pl-5 flex items-center" aria-label="Fil d'Ariane">
        <h1 class="sr-only" role="heading" aria-level="1">Menu de navigation avec fil d'Ariane</h1>
        <ol class="inline-flex items-center gap-2" role="list">
            @foreach($segments as $index => $segment)
                <li class="inline-flex items-center gap-2" role="listitem">
                    {{-- Separator (chevron) --}}
                    @if($index > 0)
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        {{-- Separator for sreen reader --}}
                        <span class="sr-only">›</span>
                    @endif

                    @if($segment['current'])
                        <span class="flex items-center text-md-medium text-gray-800 font-medium dark:text-indigo-400" aria-current="page">
                            {!! $segment['icon'] ?? '' !!}
                            {{ ucfirst($segment['label']) ?? '' }}
                        </span>
                    @else
                        <a href="{{ $segment['url'] }}" class="text-md-medium hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center">
                            {!! $segment['icon'] ?? '' !!}
                            {{ ucfirst($segment['label']) ?? '' }}
                        </a>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
</div>
