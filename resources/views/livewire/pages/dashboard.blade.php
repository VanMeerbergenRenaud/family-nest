<div>
    <p>Vous êtes connecté(e) !</p>
    <p>Bienvenue sur votre tableau de bord <a href="#" class="simple-link">Tailwind css</a>.</p>

    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}"/>

    {{-- Add a modal here to test it --}}
    <x-modal wire:model="showModal">

        <x-modal.open>
            <button type="button" class="button--blue">Créer un nouveau contact</button>
        </x-modal.open>

        <x-modal.panel>
            {{-- NEW : The heading should be fixed on the scroll --}}
            <div class="modal__panel__container__content__heading">
                Hi there I'm a fixed heading
            </div>

            <form class="form">
                <div class="form__content">
                    <h2 role="heading" aria-level="2" class="title">Créer un nouveau contact</h2>
                </div>

                <x-modal.footer>
                    <x-modal.close>
                        <button type="button" class="cancel">Annuler</button>
                    </x-modal.close>

                    <button type="submit" class="save">Créer</button>
                </x-modal.footer>
            </form>
        </x-modal.panel>
    </x-modal>
</div>
