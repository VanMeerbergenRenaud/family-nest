<div class="w-full flex flex-col gap-4">
    <div class="w-full flex flex-wrap items-center md:justify-between gap-8">

        <div class="lg:pl-4">
            <p class="text-sm text-gray-500 mb-1">{{ $family->name }}</p>
            <h2 role="heading" aria-level="2" class="font-semibold text-xl text-gray-800">Bonjour, {{ auth()->user()->name }}! 👋🏻</h2>
        </div>

        {{-- Zone de filtres --}}
        <div class="w-full md:w-auto flex flex-col sm:flex-row gap-3 items-start sm:items-end">
            <div>
                <h3 class="text-xs font-medium text-gray-500 mb-1 pl-2">Membres</h3>
                <x-dashboard.filter-member :$filters />
            </div>

            <div>
                <h3 class="text-xs font-medium text-gray-500 mb-1 pl-2">Échéances</h3>
                <x-dashboard.filter-date :$filters />
            </div>

            <div>
                <h3 class="text-xs font-medium text-gray-500 mb-1 pl-2">Statut de paiement</h3>
                <x-dashboard.filter-status :$filters />
            </div>

            @if($filters->status !== 'all' || $filters->family_member !== 'all' || $filters->range !== \App\Livewire\Pages\Dashboard\Range::All_Time)
                <button
                    type="button"
                    wire:click="resetFilters"
                    wire:loading.attr="disabled"
                    class="button-classic"
                    @if($filters->status === 'all' && $filters->family_member === 'all' && $filters->range === \App\Livewire\Pages\Dashboard\Range::All_Time) disabled @endif
                >
                    <x-svg.reset class="mr-1" />
                    Réinitialiser
                </button>
            @endif
        </div>
    </div>

    <!-- Cartes de stats (4) -->
    <livewire:pages.dashboard.stats :$filters  wire:key="stats-component-{{ $filters->status }}-{{ $filters->family_member }}" />

    {{-- Graphique par type --}}
    <livewire:pages.dashboard.chart :$filters />

    {{-- Table de factures --}}
    <livewire:pages.invoices.invoice-table :$filters />
</div>
