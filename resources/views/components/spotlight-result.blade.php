@props([
    'href' => '#',
    'text' => 'Élément vide',
    'description' => null,
    'shortcut' => null,
])

<li>
    <a href="{{ $href }}" class="relative flex items-center space-x-2 hover:bg-gray-100 rounded-lg p-2 group">
        <div class="flex-shrink-0">
            {{ $slot }}
        </div>
        <span class="text-sm font-medium text-gray-900 group-hover:text-gray-900">
            {{ $text }} <span class="text-sm-regular text-gray-400">{{ $description }}</span>
        </span>

        @if($shortcut)
            {{-- Keyboard shortcut --}}
            <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                <kbd class="pointer-events-none flex items-center gap-1.5 rounded-md border border-gray-200 bg-gray-50 px-2 py-0.5 font-sans text-xs font-medium text-gray-500">
                    {{ $shortcut }}
                </kbd>
            </div>
        @endif
    </a>
</li>
