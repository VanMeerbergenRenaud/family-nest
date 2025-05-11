@props([
    'expanded' => true,
])

<div x-data="{ showTooltip: false }" class="relative">
    <a href="{{ route('invoices.create') }}"
       id="add-invoice-link"
       wire:navigate
       class="font-semibold rounded-lg text-sm p-2.5 text-center flex-center gap-3 text-white bg-purple-500 hover:bg-purple-600 dark:bg-gray-600 dark:hover:bg-gray-700"
       @mouseenter="showTooltip = true"
       @mouseleave="showTooltip = false"
    >
        <x-svg.add class="w-4 h-4 text-white" />
        @if($expanded)
            <span class="text-sm-medium">Ajouter une facture</span>
        @endif
    </a>

    <!-- Tooltip seulement si la sidebar est rétrécie et au survol -->
    @if(!$expanded)
        <div x-cloak x-show="showTooltip">
            <x-tooltip
                text="Ajouter une facture"
                position="right"
                show="true"
                colorStyle="purple"
            />
        </div>
    @endif
</div>
