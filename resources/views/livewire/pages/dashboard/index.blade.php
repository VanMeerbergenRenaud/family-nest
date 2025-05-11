<div>
    @php
        $dashboardState = $this->getDashboardState();
    @endphp

    {{-- Cas 1: L'utilisateur n'a pas de famille --}}
    @if($dashboardState === 'no_family')
        <x-empty-state
            title="Votre famille n'a pas encore été créée"
            description="Vous n'avez pas encore de famille ? Créez-en une pour commencer à gérer vos dépenses ensemble."
        >
            <a href="{{ route('family') }}" class="button-tertiary">
                <x-svg.add2 class="text-white"/>
                Créer votre famille
            </a>
        </x-empty-state>

        {{-- Cas 2: La famille n'a aucune facture (ni active ni archivée) --}}
    @elseif($dashboardState === 'no_invoices')
        <x-empty-state
            title="Aucune facture trouvée dans votre famille"
            description="Votre famille est prête ! Ajoutez votre première facture pour commencer à gérer vos dépenses ensemble."
        >
            <a href="{{ route('invoices.create') }}" class="button-tertiary">
                <x-svg.add2 class="text-white"/>
                Ajouter ma première facture
            </a>
            <button type="button" wire:click="showInvoiceExemple" class="button-primary">
                <x-svg.help class="text-gray-900" />
                Voir un exemple
            </button>
        </x-empty-state>

        {{-- Cas 3: La famille a uniquement des factures archivées --}}
    @elseif($dashboardState === 'only_archived_invoices')
        <x-empty-state
            title="Votre famille n'a que des factures archivées"
            description="Il n'y a pas de factures actives dans votre famille, mais vous pouvez consulter les archives ou ajouter une nouvelle facture."
        >
            <a href="{{ route('invoices.create') }}" class="button-tertiary">
                <x-svg.add2 class="text-white"/>
                Ajouter une nouvelle facture
            </a>
            <a href="{{ route('invoices.archived') }}" class="button-secondary">
                <x-svg.archive class="text-white"/>
                Consulter les archives
            </a>
        </x-empty-state>

        {{-- Cas 4: La famille a des factures actives --}}
    @else
        <div class="w-full flex flex-col gap-4">
            <div class="w-full flex flex-wrap items-center md:justify-between gap-8">
                <div class="lg:pl-4">
                    <h2 role="heading" aria-level="2"  class="text-sm text-gray-500 mb-1">{{ __('Famille ') . $family->name }}</h2>
                    <p class="text-xl-semibold text-gray-800">Bonjour, {{ auth()->user()->name }}! 👋🏻</p>
                </div>

                {{-- Zone de filtres --}}
                <div class="w-full md:w-auto flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                    <div>
                        <h3 role="heading" aria-level="3" class="text-xs-medium text-gray-500 mb-1 pl-2">Membres</h3>
                        <x-dashboard.filter-member :$filters />
                    </div>

                    <div>
                        <h3 role="heading" aria-level="3" class="text-xs-medium text-gray-500 mb-1 pl-2">Échéances</h3>
                        <x-dashboard.filter-date :$filters />
                    </div>

                    <div>
                        <h3 role="heading" aria-level="3" class="text-xs-medium text-gray-500 mb-1 pl-2">Statut de paiement</h3>
                        <x-dashboard.filter-status :$filters />
                    </div>

                    @if($filters->status !== 'all' || $filters->family_member !== 'all' || $filters->range !== \App\Livewire\Pages\Dashboard\Range::All_Time)
                        <button
                            type="button"
                            wire:click="resetFilters"
                            wire:loading.attr="disabled"
                            class="button-classic"
                        >
                            <x-svg.reset class="mr-1" />
                            Réinitialiser
                        </button>
                    @endif
                </div>
            </div>

            <!-- Cartes de stats (4) -->
            <livewire:pages.dashboard.stats :$filters wire:key="stats-component-{{ $filters->status }}-{{ $filters->family_member }}" />

            {{-- Graphique par type --}}
            <livewire:pages.dashboard.chart :$filters />

            {{-- Table de factures --}}
            <livewire:pages.invoices.invoice-table :$filters :withFilters="true" />
        </div>
    @endif

    {{-- Modal d'exemple de facture (placé en dehors des conditions pour être toujours disponible) --}}
    @if($showInvoiceExempleModal)
        <x-modal wire:model="showInvoiceExempleModal">
            <x-modal.panel>
                <video controls class="w-full h-full rounded-lg" autoplay muted>
                    <source src="{{ asset('video/exemple-archive.mp4') }}" type="video/mp4">
                    Votre navigateur ne supporte pas la vidéo prévue.
                </video>
            </x-modal.panel>
        </x-modal>
    @endif
</div>
