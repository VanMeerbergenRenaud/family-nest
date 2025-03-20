<div class="relative">
    <nav class="lg:mt-2.5 py-3.5 flex items-center" aria-label="Fil d'Ariane"
         :class="{'lg:ml-64 pl-8': expanded, 'lg:mt-4 lg:ml-20 pl-12': !expanded}"
    >
        <h1 class="sr-only" role="heading" aria-level="1">Menu de navigation</h1>
        <ol class="inline-flex items-center space-x-2">
            @foreach($segments as $index => $segment)
                <li class="inline-flex items-center space-x-2">
                    @if($index > 0)
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif

                    @if($segment['current'])
                        <span class="flex items-center text-gray-800 font-medium dark:text-indigo-400">
                        @if($segment['icon'])
                                <span class="inline-block">{!! $segment['icon'] !!}</span>
                            @endif
                        <span class="text-md-medium">{{ $segment['label'] }}</span>
                    </span>
                    @else
                        <a href="{{ $segment['url'] }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center">
                            @if($segment['icon'])
                                <span class="inline-block">{!! $segment['icon'] !!}</span>
                            @endif
                            @if($segment['label'])
                                <span class="text-md-medium">{{ $segment['label'] }}</span>
                            @endif
                        </a>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    {{--<x-theme-switcher class="absolute top-3 right-3 max-lg:top-1.5"/>--}}
</div>
