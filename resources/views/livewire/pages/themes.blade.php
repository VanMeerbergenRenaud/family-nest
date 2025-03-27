<div>
    <x-empty-state
        title="Aucun thème n'a été crée jusqu'à présent"
        description="Les thèmes sont un ensemble de plusieurs tags préalablement définis pour chaque facture par l'utilisateur. Ils permettent de regrouper les factures par thème et de les retrouver plus facilement. Ainsi l'utilisateur peut créer un thème 'Sport' et y ajouter tous les tags relatifs au sport (boxe, corde à sauté, etc.)."
    >
        <a href="{{ route('themes') }}" class="button-tertiary" title="Vers la page des objectifs">
            <x-svg.add2 class="text-white" />
            Ajouter un thème
        </a>
        <button wire:click="showThemeExemple" class="button-primary">
            <x-svg.help class="text-gray-900" />
            Voir un exemple
        </button>
    </x-empty-state>

    @if($showThemeExempleModal)
        <x-modal wire:model="showThemeExempleModal">
            <x-modal.panel>
                <video controls class="w-full h-full rounded-lg" autoplay muted>
                    <source src="{{ asset('video/exemple-archive.mp4') }}" type="video/mp4">
                    Votre navigateur ne supporte pas la vidéo prévue.
                </video>
            </x-modal.panel>
        </x-modal>
    @endif
</div>
