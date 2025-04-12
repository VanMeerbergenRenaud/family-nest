@props([
    'href',
    'text',
    'description' => null,
    'state' => null,
    'shortcut' => null,
    'id' => null,
    'type' => null,
])

<li wire:key="{{ $id }}_{{ $type }}">
    <a href="{{ $href }}"
       class="relative flex items-center space-x-2 rounded-lg p-2 group {{ $this->isItemSelected($id, $type) ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}"
       wire:click.prevent="setSelectedItem('{{ $id }}', '{{ $type }}')"
       @if ($this->isItemSelected($id, $type)) id="selected-item" @endif>
        {{-- Icon or img --}}
        <div class="flex-shrink-0">
            {{ $slot }}
        </div>

        {{-- Text and description --}}
        <span class="text-sm-medium text-gray-900 dark:text-white group-hover:text-gray-900 dark:group-hover:text-white">
            {{ $text }}
            @if($description)
                <span class="text-sm-regular text-gray-400 dark:text-gray-400">{{ $description }}</span>
            @endif
        </span>

        {{-- State --}}
        @if($state)
            <div class="text-xs-medium ml-auto px-2 py-1 rounded-full bg-gray-700 text-white dark:bg-gray-700 dark:text-gray-200">
                {{ $state }}
            </div>
        @endif

        {{-- Keyboard shortcut --}}
        @if($shortcut)
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 max-lg:hidden">
                <x-shortcut :key="$shortcut" />
            </div>
        @endif
    </a>
</li>
