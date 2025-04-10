<div class="relative py-2 lg:py-3 px-5">
    <nav class="lg:pl-2 flex items-center" aria-label="Fil d'Ariane">
        <h2 role="heading" aria-level="2" class="sr-only" >Menu de navigation avec fil d'Ariane</h2>
        <ol class="inline-flex items-center gap-1" role="list">
            @foreach($segments as $index => $segment)
                <li wire:key="{{ $index }}" class="inline-flex items-center gap-1" role="listitem">
                    {{-- Separator (chevron) --}}
                    @if($index > 0)
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        {{-- Separator for sreen reader --}}
                        <span class="sr-only">›</span>
                    @endif

                    @if($segment['current'])
                        <span class="button-classic py-1 text-md-medium hover:bg-transparent" aria-current="page">
                            {!! $segment['icon'] ?? '' !!}
                            {{ ucfirst($segment['label']) ?? '' }}
                        </span>
                    @else
                        <a href="{{ $segment['url'] }}" class="button-classic py-1 text-md-medium" wire:navigate>
                            {!! $segment['icon'] ?? '' !!}
                            {{ ucfirst($segment['label']) ?? '' }}
                        </a>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
</div>
