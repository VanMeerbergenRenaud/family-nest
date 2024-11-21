<div
    x-dialog
    x-model="open"
    style="display: none"
    class="modal"
>
    <!-- Overlay -->
    <div x-dialog:overlay x-transition.opacity class="modal__overlay"></div>

    <!-- Panel -->
    <div class="modal__panel">
        <div x-dialog:panel x-transition.in x-transition.out.opacity class="modal__panel__container">

            <!-- Panel close button -->
            <div class="modal__panel__container__close-button">
                <button type="button" @click="open = false">
                    <x-svg.cross/>
                </button>
            </div>

            <!-- Panel content -->
            <div class="modal__panel__container__content">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
