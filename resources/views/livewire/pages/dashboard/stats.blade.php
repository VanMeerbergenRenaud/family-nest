<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Carte 1: Montant total -->
    <div class="relative bg-white border border-slate-200 rounded-xl p-5 overflow-hidden">
        <div class="flex flex-col">
            <h3 class="text-sm font-medium text-gray-500">Montant Total</h3>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-2xl font-bold text-gray-900">
                    {{ number_format($totalStats['totalAmount'], 2, ',', ' ') }} €
                </span>
                @if($totalStats['totalAmount'] != 0)
                    <span @class([
                        'text-xs font-medium rounded-full py-0.5 px-2 flex items-center gap-1',
                        'bg-green-100 text-green-700' => $totalStats['totalAmount'] > 0,
                        'bg-red-100 text-red-700' => $totalStats['totalAmount'] < 0,
                    ])>
                        @if($totalStats['totalAmount'] > 0)
                            <x-svg.arrow-trending-up class="w-3 h-3" />
                        @else
                            <x-svg.arrow-trending-down class="w-3 h-3" />
                        @endif
                        {{ abs($totalStats['totalAmount']) }}%
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-500 mt-2.5">
                Factures avec échéance
            </p>
            <p class="text-xs text-gray-400 mt-1">
                @if($filters->range === \App\Livewire\Pages\Dashboard\Range::All_Time)
                    Toutes les échéances
                @elseif($filters->range === \App\Livewire\Pages\Dashboard\Range::Custom && $filters->start && $filters->end)
                    {{ \Carbon\Carbon::createFromFormat('Y-m-d', $filters->start)->format('d/m/Y') }} - {{ \Carbon\Carbon::createFromFormat('Y-m-d', $filters->end)->format('d/m/Y') }}
                @elseif($filters->range === \App\Livewire\Pages\Dashboard\Range::Future)
                    Échéances futures
                @elseif($filters->range === \App\Livewire\Pages\Dashboard\Range::Next_7)
                    {{ \App\Livewire\Pages\Dashboard\Range::Next_7->label() }}
                @elseif($filters->range === \App\Livewire\Pages\Dashboard\Range::Next_30)
                    {{ \App\Livewire\Pages\Dashboard\Range::Next_30->label() }}
                @elseif($filters->range === \App\Livewire\Pages\Dashboard\Range::Today)
                    Aujourd'hui
                @elseif($filters->range === \App\Livewire\Pages\Dashboard\Range::This_Week)
                    Cette semaine
                @elseif($filters->range === \App\Livewire\Pages\Dashboard\Range::This_Month)
                    Ce mois-ci
                @elseif($filters->range === \App\Livewire\Pages\Dashboard\Range::Year)
                    Cette année
                @else
                    {{ $filters->range->label() }}
                @endif
            </p>
        </div>
    </div>

    <!-- Carte 2: Montant moyen -->
    <div class="relative bg-white border border-slate-200 rounded-xl p-5 overflow-hidden">
        <div class="flex flex-col">
            <h3 class="text-sm font-medium text-gray-500">Montant Moyen</h3>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-2xl font-bold text-gray-900">
                    {{ number_format($totalStats['averageAmount'], 2, ',', ' ') }} €
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-2.5">
                Par facture
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Pour la période d'échéance sélectionnée
            </p>
        </div>
    </div>

    <!-- Carte 3: Nombre de factures -->
    <div class="relative bg-white border border-slate-200 rounded-xl p-5 overflow-hidden">
        <div class="flex flex-col">
            <h3 class="text-sm font-medium text-gray-500">Factures</h3>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-2xl font-bold text-gray-900">
                    {{ number_format($totalStats['invoiceCount'], 0, ',', ' ') }}
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-2.5">
                Total des factures
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Pour la période d'échéance sélectionnée
            </p>
        </div>
    </div>

    <!-- Carte 4: Factures payées -->
    <div class="relative bg-white border border-slate-200 rounded-xl p-5 overflow-hidden">
        <div class="flex flex-col">
            <h3 class="text-sm font-medium text-gray-500">Factures Payées</h3>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-2xl font-bold text-gray-900">
                    {{ number_format($totalStats['paidInvoices'], 0, ',', ' ') }}
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-2.5">
                @if($totalStats['invoiceCount'] > 0)
                    {{ number_format(($totalStats['paidInvoices'] / $totalStats['invoiceCount']) * 100, 0) }}% du total
                @else
                    0% du total
                @endif
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Pour la période d'échéance sélectionnée
            </p>
        </div>
    </div>
</div>
