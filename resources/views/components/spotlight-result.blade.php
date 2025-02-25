@props([
    'href' => '#',
    'text' => 'Élément vide',
    'description' => null,
    'shortcut' => null,
])

<li>
    <a href="{{ $href }}" class="relative flex items-center space-x-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg p-2 group">
        {{-- Icon or img --}}
        <div class="flex-shrink-0">
            {{ $slot }}
        </div>

        {{-- Text and description --}}
        <span class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-gray-900 dark:group-hover:text-white">
            {{ $text }} <span class="text-sm-regular text-gray-400 dark:text-gray-400">{{ $description }}</span>
        </span>

        {{-- Keyboard shortcut --}}
        @if($shortcut)
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 max-lg:hidden">
                <x-shortcut :key="$shortcut" />
            </div>
        @endif
    </a>
</li>
