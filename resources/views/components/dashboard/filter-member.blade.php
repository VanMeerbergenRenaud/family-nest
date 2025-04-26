<div
    class="flex items-center gap-2"
    wire:key="filter-family-member-{{ $filters->status }}"
>
    <x-menu>
        <x-menu.button class="flex items-center gap-2 text-sm px-3 py-1.5 rounded-lg border bg-white text-gray-700 hover:bg-gray-50">
            @php
                $selectedMember = $filters->familyMembers()->firstWhere('id', $filters->family_member);
                $selectedMemberName = $selectedMember ? $selectedMember['name'] : 'Tous les membres';
            @endphp

            <span>{{ $selectedMemberName }}</span>
            <x-svg.chevron-down class="h-4 w-4 text-gray-400" />
        </x-menu.button>

        <x-menu.items class="w-56">
            @foreach ($filters->familyMembers() as $member)
                <x-menu.item
                    wire:click="$set('filters.family_member', '{{ $member['id'] }}')"
                    class="{{ $filters->family_member === $member['id'] ? 'bg-blue-50 text-blue-600' : '' }} flex justify-between"
                >
                    <span>{{ $member['name'] }}</span>
                    <span class="text-gray-500 text-xs">{{ $member['invoice_count'] }}</span>
                </x-menu.item>
            @endforeach
        </x-menu.items>
    </x-menu>
</div>
