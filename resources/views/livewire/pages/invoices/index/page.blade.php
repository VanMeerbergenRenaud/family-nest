<div>
    <h2 role="heading" aria-level="2" class="sr-only">{{ __('Factures de') }} {{ auth()->user()->name }}</h2>

    @if($this->userHasInvoices())
        {{-- Section des folders --}}
        <livewire:pages.invoices.index.folders />

        {{-- Section des factures récentes --}}
        <livewire:pages.invoices.index.recents />

        {{-- Table de factures --}}
        <livewire:pages.invoices.invoice-table :filters="$filters" :withFilters="false" />
    @else
        {{-- Empty state --}}
        <div class="w-full mx-auto bg-white rounded-3xl border border-slate-200">
            <div class="relative px-8 pb-8 flex-center flex-col text-center overflow-hidden">
                <div class="absolute -top-10 w-full h-100 pointer-events-none" style="background-image: url('{{ asset('img/empty-invoice.png') }}'); background-size: contain; background-position: center; background-repeat: no-repeat;"></div>
                <!-- Textes -->
                <h3 role="heading" aria-level="3" class="display-xs-semibold text-gray-900 mt-46 mb-4">
                    {{ __('Aucune facture créée jusqu\'à présent') }}
                </h3>

                <p class="text-gray-500 mb-8 max-w-md mx-auto">
                    {{ __('Vous n\'avez pas encore créé de facture. Pour commencer, cliquez sur le bouton ci-dessous pour ajouter votre première facture sans plus attendre.') }}
                </p>

                <!-- Buttons -->
                <div class="flex-center flex-wrap gap-2">
                    @if($this->userHasArchivedInvoices())
                        <a href="{{ route('invoices.create') }}" class="button-tertiary">
                            <x-svg.add2 class="text-white"/>
                            {{ __('Ajouter une nouvelle facture') }}
                        </a>

                        <a href="{{ route('invoices.archived') }}" class="button-secondary">
                            <x-svg.archive class="text-white"/>
                            {{ __('Voir mes archives') }}
                        </a>
                    @else
                        <a href="{{ route('invoices.create') }}" class="button-tertiary">
                            <x-svg.add2 class="text-white"/>
                            {{ __('Ajouter ma première facture') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
