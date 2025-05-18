@props([
    'filters'
])

<div wire:key="filter-date-{{ $filters->status }}-{{ $filters->family_member }}"
     x-data="{ refreshed: 0 }"
     x-init="$wire.on('familyMemberChanged', () => { refreshed++ }); $wire.on('statusChanged', () => { refreshed++ })">
    <x-menu>
        <x-menu.button class="min-w-44 button-primary justify-between">
            {{ $filters->range->label($filters->start, $filters->end) }}
            <x-svg.chevron-down class="ml-1 text-gray-500 flex-shrink-0"/>
        </x-menu.button>

        <x-menu.items class="max-h-[24.5rem] overflow-y-auto">
            <x-menu.item
                    wire:click="$set('filters.range', '{{ \App\Livewire\Pages\Dashboard\RangeEnum::All_Time }}')"
                    class="{{ $filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::All_Time ? 'bg-teal-100 text-teal-900 font-medium' : '' }} justify-between">
                {{ \App\Livewire\Pages\Dashboard\RangeEnum::All_Time->label() }}
                @if ($filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::All_Time)
                    <x-svg.check class="relative left-1.5 stroke-0 text-teal-700"/>
                @endif
            </x-menu.item>

            <x-menu.divider/>

            <x-menu.item
                    wire:click="$set('filters.range', '{{ \App\Livewire\Pages\Dashboard\RangeEnum::Year }}')"
                    class="{{ $filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::Year ? 'bg-teal-100 text-teal-900 font-medium' : '' }} justify-between">
                {{ \App\Livewire\Pages\Dashboard\RangeEnum::Year->label() }}
                @if ($filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::Year)
                    <x-svg.check class="relative left-1.5 stroke-0 text-teal-700"/>
                @endif
            </x-menu.item>

            <x-menu.item
                    wire:click="$set('filters.range', '{{ \App\Livewire\Pages\Dashboard\RangeEnum::This_Month }}')"
                    class="{{ $filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::This_Month ? 'bg-teal-100 text-teal-900 font-medium' : '' }} justify-between">
                {{ \App\Livewire\Pages\Dashboard\RangeEnum::This_Month->label() }}
                @if ($filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::This_Month)
                    <x-svg.check class="relative left-1.5 stroke-0 text-teal-700"/>
                @endif
            </x-menu.item>

            <x-menu.item
                    wire:click="$set('filters.range', '{{ \App\Livewire\Pages\Dashboard\RangeEnum::This_Week }}')"
                    class="{{ $filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::This_Week ? 'bg-teal-100 text-teal-900 font-medium' : '' }} justify-between">
                {{ \App\Livewire\Pages\Dashboard\RangeEnum::This_Week->label() }}
                @if ($filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::This_Week)
                    <x-svg.check class="relative left-1.5 stroke-0 text-teal-700"/>
                @endif
            </x-menu.item>

            <x-menu.item
                    wire:click="$set('filters.range', '{{ \App\Livewire\Pages\Dashboard\RangeEnum::Today }}')"
                    class="{{ $filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::Today ? 'bg-teal-100 text-teal-900 font-medium' : '' }} justify-between">
                {{ \App\Livewire\Pages\Dashboard\RangeEnum::Today->label() }}
                @if ($filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::Today)
                    <x-svg.check class="relative left-1.5 stroke-0 text-teal-700"/>
                @endif
            </x-menu.item>

            <x-menu.divider/>

            <x-menu.item
                    wire:click="$set('filters.range', '{{ \App\Livewire\Pages\Dashboard\RangeEnum::Next_7 }}')"
                    class="{{ $filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::Next_7 ? 'bg-teal-100 text-teal-900 font-medium' : '' }} justify-between">
                {{ \App\Livewire\Pages\Dashboard\RangeEnum::Next_7->label() }}
                @if ($filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::Next_7)
                    <x-svg.check class="relative left-1.5 stroke-0 text-teal-700"/>
                @endif
            </x-menu.item>

            <x-menu.item
                    wire:click="$set('filters.range', '{{ \App\Livewire\Pages\Dashboard\RangeEnum::Next_30 }}')"
                    class="{{ $filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::Next_30 ? 'bg-teal-100 text-teal-900 font-medium' : '' }} justify-between">
                {{ \App\Livewire\Pages\Dashboard\RangeEnum::Next_30->label() }}
                @if ($filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::Next_30)
                    <x-svg.check class="relative left-1.5 stroke-0 text-teal-700"/>
                @endif
            </x-menu.item>

            <x-menu.item
                    wire:click="$set('filters.range', '{{ \App\Livewire\Pages\Dashboard\RangeEnum::Future }}')"
                    class="{{ $filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::Future ? 'bg-teal-100 text-teal-900 font-medium' : '' }} justify-between">
                {{ \App\Livewire\Pages\Dashboard\RangeEnum::Future->label() }}
                @if ($filters->range === \App\Livewire\Pages\Dashboard\RangeEnum::Future)
                    <x-svg.check class="relative left-1.5 stroke-0 text-teal-700"/>
                @endif
            </x-menu.item>

            <x-menu.divider/>

            <div x-data="{ showCustomRangePanel: $wire.filters.range === '{{ \App\Livewire\Pages\Dashboard\RangeEnum::Custom }}' ? true : false }">
                <x-menu.item
                        type="button"
                        x-on:click="showCustomRangePanel = ! showCustomRangePanel"
                        class="justify-between"
                >
                    {{ __('Définir des dates précises') }}
                    <x-svg.chevron-down x-show="! showCustomRangePanel" class="relative left-1.5 text-gray-500"/>
                    <x-svg.chevron-up x-show="showCustomRangePanel" class="relative left-1.5 text-gray-500"/>
                </x-menu.item>

                <form
                        x-show="showCustomRangePanel"
                        x-collapse class="flex flex-col p-2 gap-2.5"
                        wire:submit="$set('filters.range', '{{ \App\Livewire\Pages\Dashboard\RangeEnum::Custom }}')"
                >
                    <div class="flex justify-between items-center gap-2.5">
                        <input
                                wire:model.live="filters.start"
                                type="date"
                                class="m-0 px-3 py-2 block w-full appearance-none bg-white text-sm text-gray-600 border border-slate-200 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                                required
                        >

                        <span class="text-sm text-gray-700">et</span>

                        <input
                                wire:model.live="filters.end"
                                type="date"
                                class="m-0 px-3 py-2 block w-full appearance-none bg-white text-sm text-gray-600 border border-slate-200 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                                required
                        >
                    </div>

                    <button type="submit" class="flex-1 button-tertiary justify-center">
                        Appliquer
                    </button>
                </form>
            </div>
        </x-menu.items>
    </x-menu>
</div>
