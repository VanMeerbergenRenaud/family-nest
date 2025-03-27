<div>
    <div>
        <x-empty-state
            title="Salut {{ $user->name ?? 'inconnu' }} !"
            description="Votre tableau de bord est vide. Vous n'avez pas encore d'objectifs, de thèmes ou de factures. Veuillez commencer par ajouter une facture !"
        >
            <a href="{{ route('invoices.create') }}" class="button-tertiary" title="Vers la page des factures">
                <x-svg.add2 class="text-white" />
                Ajouter une facture
            </a>
            <button wire:click="showDashboardExemple" class="button-primary">
                <x-svg.help class="text-gray-900" />
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
    </div>
</div>
