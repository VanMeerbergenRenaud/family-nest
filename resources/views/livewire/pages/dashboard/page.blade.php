<div class="w-full flex flex-col gap-8">
    <div class="gap-4 flex flex-col items-start justify-start sm:flex-row sm:justify-between sm:items-center">
        <div class="flex flex-col gap-1">
            <h1 class="font-semibold text-3xl text-gray-800">Tableau de bord</h1>
            <p class="hidden sm:block text-sm text-gray-500">{{ $family->name }}</p>
        </div>
    </div>

    {{-- Zone de filtres --}}
    <div class="flex flex-col sm:flex-row gap-4">
        {{-- Filtre par statut --}}
        <div class="flex-1">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Statut de paiement</h3>
            <x-dashboard.filter-status :$filters />
        </div>

        {{-- Filtre par membre --}}
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-2">Membres</h3>
            <x-dashboard.filter-member :$filters />
        </div>
    </div>

    {{-- Graphique par type --}}
    <livewire:pages.dashboard.chart :$filters />

    {{-- Table de factures --}}
    <livewire:pages.dashboard.table :$filters />
</div>
