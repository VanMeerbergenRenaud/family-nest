<div
    x-dialog
    x-model="open"
    style="display: none"
    class="fixed inset-0 overflow-y-auto z-55"
>
    <!-- Overlay -->
    <div x-dialog:overlay x-transition.opacity class="fixed top-0 right-0 bottom-0 left-0 bg-[rgba(0,0,0,0.25)]"></div>

    <!-- Panel -->
    <div class="relative min-h-full p-4 flex items-center">
        <div x-dialog:panel x-transition.in x-transition.out.opacity class="mx-auto relative w-full max-w-[40rem] bg-white rounded-xl shadow-[0_0_0.5rem_rgba(0,0,0,0.2)] overflow-hidden">

            <!-- Panel close button -->
            <div class="absolute top-0 right-0 p-4 z-30">
                <button type="button" @click="open = false" class="p-3 text-[#46474C] bg-[#F0F1F7] rounded-full flex items-center justify-center hover:text-blue-950 hover:bg-[#ebebf1]">
                    <x-svg.cross/>
                </button>
            </div>

            <!-- Panel content -->
            <div class="max-h-[80vh] overflow-y-scroll">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
