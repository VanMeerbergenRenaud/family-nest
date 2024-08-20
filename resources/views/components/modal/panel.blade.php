<template x-teleport="body">
    <div
        x-dialog
        x-model="dialogOpen"
        style="display: none"
        class="modal"
    >
        <!-- Overlay -->
        <div x-dialog:overlay x-transition:enter.opacity class="modal__overlay"></div>

        <!-- Panel -->
        <div class="panel">
            <div x-dialog:panel x-transition.in class="dialog">

                <!-- Close Button -->
                <div class="dialog__close-button">
                    <button type="button" @click="dialogOpen = false">
                        <span class="sr-only">Fermer la modale</span>
                        <x-svg.cross />
                    </button>
                </div>

                <!-- Panel -->
                <div class="dialog__panel">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</template>
