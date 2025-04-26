@props([
    'filters'
])

<div
    class="flex items-center gap-2"
    wire:key="filter-status-{{ $filters->family_member }}"
    x-data="{ refreshed: 0 }"
    x-init="$wire.on('familyMemberChanged', () => { refreshed++ })"
>
    <x-menu>
        <x-menu.button class="min-w-44 button-primary justify-between">
            @php
                $selectedStatus = $filters->statuses()->firstWhere('value', $filters->status);
                $selectedStatusName = $selectedStatus ? $selectedStatus['label'] : 'Tous';
            @endphp
            {{ $selectedStatusName }}
            <x-svg.chevron-down class="ml-1 text-gray-500" />
        </x-menu.button>

        <x-menu.items class="w-56">
            @foreach ($filters->statuses() as $status)
                <x-menu.item
                    wire:click="$set('filters.status', '{{ $status['value'] }}')"
                    class="{{ $filters->status === $status['value'] ? 'bg-teal-100 text-teal-900 font-medium' : '' }} flex justify-between"
                >
                    {{ $status['label'] }}
                    <span class="text-gray-500 text-xs">{{ $status['count'] }}</span>
                </x-menu.item>
            @endforeach
        </x-menu.items>
    </x-menu>
</div>
