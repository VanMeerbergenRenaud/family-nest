@props([
    'filters'
])

<div>
    <x-menu>
        <x-menu.button class="min-w-44 button-primary justify-between">
            {{ $filters->range->label($filters->start, $filters->end) }}
            <x-svg.chevron-down />
        </x-menu.button>

        <x-menu.items>
            @foreach (\App\Livewire\Pages\Dashboard\Range::cases() as $range)
                @if ($range === \App\Livewire\Pages\Dashboard\Range::Custom)
                    <div x-data="{ showCustomRangePanel: $wire.filters.range === '{{ \App\Livewire\Pages\Dashboard\Range::Custom }}' ? true : false }">
                        <x-menu.item type="button" x-on:click="showCustomRangePanel = ! showCustomRangePanel" class="justify-between">
                            <div class="text-sm">Custom Range</div>

                            <x-svg.chevron-down x-show="! showCustomRangePanel" />
                            <x-svg.chevron-up x-show="showCustomRangePanel" />
                        </x-menu.item>

                        <form
                            x-show="showCustomRangePanel"
                            x-collapse class="flex flex-col px-3 py-2 gap-4"
                            wire:submit="$set('filters.range', '{{ \App\Livewire\Pages\Dashboard\Range::Custom }}')"
                        >
                            <div class="flex justify-between items-center gap-2">
                                <input wire:model="filters.start" type="date" class="text-sm text-gray-700 rounded border border-gray-300 bg-white px-2 py-1" required>

                                <span class="text-sm text-gray-700">et</span>

                                <input wire:model="filters.end" type="date" class="text-sm text-gray-700 rounded border border-gray-300 bg-white px-2 py-1" required>
                            </div>

                            <div class="flex">
                                <button type="submit" class="w-full button-tertiary justify-center">
                                    Appliquer
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                   <x-menu.item
                        wire:click="$set('filters.range', '{{ $range }}')"
                        class="{{ $range === $filters->range ? 'bg-teal-100 text-teal-900 font-medium' : '' }} justify-between">
                        {{ $range->label() }}
                        @if ($range === $filters->range)
                            <x-svg.check class="stroke-0 text-teal-700" />
                        @endif
                    </x-menu.item>
                @endif
            @endforeach
        </x-menu.items>
    </x-menu>
</div>
