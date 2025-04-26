@props([
    'filters'
])

<div
    class="flex items-center gap-2"
    wire:key="filter-family-member-{{ $filters->status }}"
>
    <x-menu>
        <x-menu.button class="min-w-44 button-primary justify-between">
            @php
                $selectedMember = $filters->familyMembers()->firstWhere('id', $filters->family_member);
                $selectedMemberName = $selectedMember ? $selectedMember['name'] : 'Tous les membres';
            @endphp
            {{ $selectedMemberName }}
            <x-svg.chevron-down class="text-gray-400" />
        </x-menu.button>

        <x-menu.items class="w-56">
            @foreach ($filters->familyMembers() as $member)
                <x-menu.item
                    wire:click="$set('filters.family_member', '{{ $member['id'] }}')"
                    class="{{ $filters->family_member == $member['id'] ? 'bg-teal-100 text-teal-900 font-medium' : '' }} flex justify-between items-center"
                >
                    {{ $member['name'] }}
                    <span class="text-gray-500 text-xs">{{ $member['invoice_count'] }}</span>
                </x-menu.item>
            @endforeach
        </x-menu.items>
    </x-menu>
</div>
