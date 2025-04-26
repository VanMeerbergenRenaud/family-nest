<div class="w-full flex flex-col gap-4">
    <div class="w-full flex flex-wrap items-center md:justify-between gap-8">

        <div class="lg:pl-4">
            <h2 role="heading" aria-level="2" class="font-semibold text-xl text-gray-800">Bonjour, {{ auth()->user()->name }}! 👋🏻</h2>
            <p class="text-sm text-gray-500">{{ $family->name }}</p>
        </div>

        {{-- Zone de filtres --}}
        <div class="flex flex-wrap gap-4 items-center">
            <div>
                <h3 class="pl-2 text-sm-medium text-gray-500 mb-2">Date</h3>
                <x-dashboard.filter-date :$filters />
            </div>
            <div>
                <h3 class="pl-2 text-sm-medium text-gray-500 mb-2">Statut de paiement</h3>
                <x-dashboard.filter-status :$filters />
            </div>
            <div>
                <h3 class="pl-2 text-sm-medium text-gray-500 mb-2">Membres</h3>
                <x-dashboard.filter-member :$filters />
            </div>
        </div>
    </div>

    <!-- Cartes de stats (4) -->
    <livewire:pages.dashboard.stats :$filters  wire:key="stats-component-{{ $filters->status }}-{{ $filters->family_member }}" />

    {{-- Graphique par type --}}
    <livewire:pages.dashboard.chart :$filters />

    {{-- Table de factures --}}
    <livewire:pages.dashboard.table :$filters />
</div>
