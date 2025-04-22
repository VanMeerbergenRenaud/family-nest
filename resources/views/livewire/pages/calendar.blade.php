<div>
    <x-empty-state
        title="Aucune date n'a été déterminé pour l'instant"
        description="Vous êtes déterminé à mieux gérer vos finances et vos factures ? N’hésitez pas à vous fixer des dates pour vos factures, que ce soit pour vos abonnements ou vos dépenses par exemple."
    >
        <a href="{{ route('invoices.index') }}" class="button-tertiary" title="Vers la page des factures">
            <x-svg.add2 class="text-white" />
            Associé une facture à une date
        </a>
        <button wire:click="showCalendarExemple" class="button-primary">
            <x-svg.help class="text-gray-900" />
            Voir un exemple
        </button>
    </x-empty-state>

    @if($showCalendarExempleModal)
        <x-modal wire:model="showCalendarExempleModal">
            <x-modal.panel>
                <video controls class="w-full h-full rounded-lg" autoplay muted>
                    <source src="{{ asset('video/exemple-archive.mp4') }}" type="video/mp4">
                    Votre navigateur ne supporte pas la vidéo prévue.
                </video>
            </x-modal.panel>
        </x-modal>
    @endif
</div>
