<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Carte 1: Montant Total -->
    <div class="relative bg-white border border-slate-200 rounded-xl p-5 overflow-hidden">
        <div class="flex flex-col">
            <h3 class="text-sm-medium text-gray-600">Montant Total</h3>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-2xl font-bold text-gray-900">
                    {{ number_format($this->statistics['totalAmount'], 2, ',', ' ') }} €
                </span>
            </div>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-sm text-gray-700">Payé: {{ number_format($this->statistics['paidAmount'], 2, ',', ' ') }} €</span>
                <span class="ml-1 px-2 py-0.5 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700">
                    {{ $this->statistics['paidPercentage'] }}%
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-2.5">
                {{ $this->statistics['invoiceCount'] }} factures au total
            </p>
        </div>
    </div>

    <!-- Carte 2: Montant Impayé à Échéance Dépassée -->
    <div class="relative bg-white border border-slate-200 rounded-xl p-5 overflow-hidden">
        <div class="flex flex-col">
            <h3 class="text-sm-medium text-gray-600">Montant en Retard</h3>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-2xl font-bold {{ $this->statistics['overdueAmount'] > 0 ? 'text-rose-600' : 'text-gray-900' }}">
                    {{ number_format($this->statistics['overdueAmount'], 2, ',', ' ') }} €
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Factures impayées dépassant la date d'échéance
            </p>
            <p class="text-xs text-gray-400 mt-2">
                @if($this->statistics['totalAmount'] > 0 && $this->statistics['overdueAmount'] > 0)
                    {{ round(($this->statistics['overdueAmount'] / $this->statistics['totalAmount']) * 100) }}% du montant total
                @else
                    Aucun retard pour le moment
                @endif
            </p>
        </div>
    </div>

    <!-- Carte 3: Échéances du mois en cours -->
    <div class="relative bg-white border border-slate-200 rounded-xl p-5 overflow-hidden">
        <div class="flex flex-col">
            <h3 class="text-sm-medium text-gray-600">Échéances ce Mois</h3>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-2xl font-bold text-gray-900">
                    {{ number_format($this->statistics['thisMonthAmount'], 2, ',', ' ') }} €
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Total des factures pour {{ Carbon\Carbon::now()->locale('fr')->monthName }}
            </p>
            <p class="text-xs text-gray-400 mt-2">
                @if($this->statistics['totalAmount'] > 0)
                    {{ round(($this->statistics['thisMonthAmount'] / $this->statistics['totalAmount']) * 100) }}% du montant total filtré
                @endif
            </p>
        </div>
    </div>

    <!-- Carte 4: Factures à venir dans les 7 jours -->
    <div class="relative bg-white border border-slate-200 rounded-xl p-5 overflow-hidden">
        <div class="flex flex-col">
            <h3 class="text-sm-medium text-gray-600">À Payer Prochainement</h3>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-2xl font-bold text-gray-900">
                    {{ $this->statistics['upcomingDueCount'] }}
                </span>
                <span class="text-sm text-gray-600 font-medium">Facture(s)</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Arrivent à échéance dans les 7 prochains jours
            </p>
            <p class="text-xs text-gray-400 mt-2">
                Pensez à planifier vos paiements
            </p>
        </div>
    </div>
</div>
