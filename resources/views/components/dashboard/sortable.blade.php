@props(['column', 'sortCol', 'sortAsc'])

<button wire:click="sortBy('{{ $column }}')" {{ $attributes->merge(['class' => 'flex items-center gap-2 group']) }}>
    {{ $slot }}

    @if ($sortCol === $column)
        <div class="text-gray-400">
            @if ($sortAsc)
                <x-svg.arrow-long-up />
            @else
                <x-svg.arrow-long-down />
            @endif
        </div>
    @else
        <div class="text-gray-400 opacity-0 group-hover:opacity-100">
            <x-svg.arrows-up-down />
        </div>
    @endif
</button>
