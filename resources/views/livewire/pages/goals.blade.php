<div>
    <x-empty-state
        title="Aucun objectif n'a été crée jusqu'à présent"
        description="Vous êtes déterminé à mieux gérer vos finances et vos factures ? N’hésitez pas à vous fixer des objectifs à respecter, que ce soit pour vos abonnements ou vos dépenses par exemple."
    >
        <a href="{{ route('goals') }}" class="button-tertiary isDisabled" title="Vers la page des objectifs">
            <x-svg.target class="text-white" />
            Se fixer un objectif
        </a>
        <button wire:click="showGoalExemple" class="button-primary">
            <x-svg.help class="text-gray-900" />
            Voir un exemple
        </button>
    </x-empty-state>

    @if($showGoalExempleModal)
        <x-modal wire:model="showGoalExempleModal">
            <x-modal.panel>
                <video controls class="w-full h-full rounded-lg" autoplay muted>
                    <source src="{{ asset('video/exemple-archive.mp4') }}" type="video/mp4">
                    Votre navigateur ne supporte pas la vidéo prévue.
                </video>
            </x-modal.panel>
        </x-modal>
    @endif
</div>
