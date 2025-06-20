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
        <div class="relative isolate overflow-hidden rounded-xl border border-slate-200 bg-white px-4 lg:px-10">
            <div class="mx-auto grid grid-cols-1 gap-x-8 gap-y-12 lg:grid-cols-2 lg:items-center">

                <div class="mt-6">
                    <div class="mb-5 ml-2 inline-flex items-center gap-x-2 rounded-full bg-slate-100 px-3 py-1 text-xs-medium text-slate-600 ring-1 ring-inset ring-slate-200">
                        <x-svg.changelog class="w-3.5 h-3.5 text-slate-500"/>
                        Gestion de factures
                    </div>
                    <h2 class="pl-2 text-3xl font-semibold text-slate-900 leading-tight">
                        Aucune facture trouvée dans votre famille
                    </h2>
                    <p class="mt-4 pl-2  text-slate-600">
                        Votre famille a été créée ! Ajoutez maintenant votre première facture pour commencer à gérer vos dépenses, seul ou ensemble.
                    </p>
                    <div class="mt-8 flex items-center flex-wrap gap-2">
                        <a href="{{ route('invoices.create') }}" class="button-primary" title="Vers la page de création de facture">
                            <x-svg.add2 class="text-slate-900"/>
                            Ajouter ma première facture
                        </a>

                        <a href="{{ asset('video/tutorials/create_invoice.mp4') }}"
                           target="_blank"
                           title="Regarder le tutoriel"
                           class="button-classic px-3 py-2 text-slate-700 hover:text-slate-900 transition-colors"
                        >
                            Voir le tutoriel <span aria-hidden="true"> → </span>
                        </a>
                    </div>
                </div>

                <img
                    src="{{ asset('img/onboarding/basics.png') }}"
                    alt="Exemple d'une facture de l'application"
                    class="relative top-0 lg:top-8 bottom-0 -right-12 object-contain rounded-l-xl border-t border-l border-slate-200"
                >
            </div>
        </div>

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
            <div class="w-full flex flex-wrap items-center md:justify-between gap-4 lg:gap-8">
                <div class="lg:pl-4">
                    <h2 role="heading" aria-level="2" class="text-sm text-gray-500 mb-1">
                        {{ __('Famille ') . $family->name }}
                    </h2>
                    <p class="text-xl-semibold text-gray-800">Bonjour, {{ auth()->user()->name }}! 👋🏻</p>
                </div>

                {{-- Zone de filtres --}}
                <div class="w-full md:w-auto flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                    <div>
                        <h3 role="heading" aria-level="3" class="text-xs-medium text-gray-500 mb-1 pl-2">Membres</h3>
                        <x-dashboard.filter-member :$filters/>
                    </div>

                    <div>
                        <h3 role="heading" aria-level="3" class="text-xs-medium text-gray-500 mb-1 pl-2">Échéances</h3>
                        <x-dashboard.filter-date :$filters/>
                    </div>

                    <div>
                        <h3 role="heading" aria-level="3" class="text-xs-medium text-gray-500 mb-1 pl-2">Statut de
                            paiement</h3>
                        <x-dashboard.filter-status :$filters/>
                    </div>

                    @if($filters->status !== 'all' || $filters->family_member !== 'all' || $filters->range !== \App\Livewire\Pages\Dashboard\RangeEnum::All_Time)
                        <button
                            type="button"
                            wire:click="resetFilters"
                            wire:loading.attr="disabled"
                            class="button-classic"
                        >
                            <x-svg.reset class="mr-1"/>
                            Réinitialiser
                        </button>
                    @endif
                </div>
            </div>

            <!-- Cartes de stats (4) -->
            <livewire:pages.dashboard.stats :$filters
                                            wire:key="stats-component-{{ $filters->status }}-{{ $filters->family_member }}"/>

            {{-- Graphique par type --}}
            <livewire:pages.dashboard.chart :$filters/>

            {{-- Table de factures --}}
            <livewire:pages.invoices.invoice-table :$filters :withFilters="true"/>
        </div>
    @endif
</div>
