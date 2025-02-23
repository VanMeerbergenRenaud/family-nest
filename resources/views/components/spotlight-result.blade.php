@props([
    'href' => '#',
    'text' => 'Élément vide',
    'description' => null,
    'shortcut' => null,
])

<li>
    <a href="{{ $href }}" class="relative flex items-center space-x-2 hover:bg-gray-100 rounded-lg p-2 group">
        {{-- Icon or img --}}
        <div class="flex-shrink-0">
            {{ $slot }}
        </div>

        {{-- Text and description --}}
        <span class="text-sm font-medium text-gray-900 group-hover:text-gray-900">
            {{ $text }} <span class="text-sm-regular text-gray-400">{{ $description }}</span>
        </span>

        {{-- Keyboard shortcut --}}
        @if($shortcut)
            <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                <x-shortcut :key="$shortcut" />
            </div>
        @endif
    </a>
</li>
