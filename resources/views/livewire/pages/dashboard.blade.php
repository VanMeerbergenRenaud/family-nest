<div class="flex gap-4 flex-col md:flex-row">
    @if($invoices->count() === 0)
        <x-empty-state
            title="Salut {{ $user->name ?? 'inconnu' }} !"
            description="Votre tableau de bord est vide. Vous n'avez pas encore d'objectifs, de thèmes ou de factures. Veuillez commencer par ajouter une facture !"
        >
            <a href="{{ route('invoices.create') }}" class="button-tertiary" title="Vers la page des factures" wire:navigate>
                <x-svg.add2 class="text-white"/>
                Ajouter une facture
            </a>
            <button wire:click="showDashboardExemple" class="button-primary">
                <x-svg.help class="text-gray-900"/>
                Voir un exemple
            </button>
        </x-empty-state>

        @if($showDashboardExempleModal)
            <x-modal wire:model="showDashboardExempleModal">
                <x-modal.panel>
                    <video controls class="w-full h-full rounded-lg" autoplay muted>
                        <source src="{{ asset('video/exemple-archive.mp4') }}" type="video/mp4">
                        Votre navigateur ne supporte pas la vidéo prévue.
                    </video>
                </x-modal.panel>
            </x-modal>
        @endif
    @elseif($allInvoicesOfFamily)
        <div class="bg-white rounded-xl p-4 border border-slate-200">
            <p class="text-lg-medium pl-2 mb-3">Factures de la famille : {{ $family->name ?? 'Non précisé' }}</p>
            <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach(($allInvoicesOfFamily) as $invoice)
                    <li wire:key="invoice-{{ $invoice->id }}">
                        <a wire:navigate
                           href="{{ route('invoices.show', $invoice) }}"
                           class="block bg-white hover:bg-gray-50 transition duration-150 rounded-lg p-4 border border-slate-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <x-svg.document class="w-5 h-5 text-gray-500"/>
                                    <p class="text-sm text-gray-700">{{ $invoice->name ?? 'Non précisé' }}</p>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-semibold">{{ number_format($invoice->amount, 2, ',', ' ') }} €</span>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @elseif($allInvoicesOfUser)
        <div class="bg-white rounded-xl p-4 border border-slate-200">
            <p class="text-lg-medium pl-2 mb-3">Factures de : {{ auth()->user()->name }}</p>
            <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach(($allInvoicesOfUser) as $invoice)
                    <li wire:key="invoice-{{ $invoice->id }}">
                        <a wire:navigate
                           href="{{ route('invoices.show', $invoice) }}"
                           class="block bg-white hover:bg-gray-50 transition duration-150 rounded-lg p-4 border border-slate-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <x-svg.document class="w-5 h-5 text-gray-500"/>
                                    <p class="text-sm text-gray-700">{{ $invoice->name ?? 'Non précisé' }}</p>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-semibold">{{ number_format($invoice->amount, 2, ',', ' ') }} €</span>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
