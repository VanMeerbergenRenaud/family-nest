<div
    x-dialog
    x-model="open"
    style="display: none"
    class="fixed inset-0 overflow-y-auto z-55"
>
    <!-- Overlay -->
    <div x-dialog:overlay x-transition.opacity class="fixed top-0 right-0 bottom-0 left-0 bg-[rgba(0,0,0,0.25)] dark:bg-[rgba(0,0,0,0.5)]"></div>

    <!-- Panel -->
    <div class="relative min-h-full p-4 flex items-center">
        <div x-dialog:panel x-transition.in x-transition.out.opacity class="mx-auto relative w-full max-w-[40rem] bg-white dark:bg-gray-800 rounded-xl shadow-[0_0_0.5rem_rgba(0,0,0,0.2)] dark:shadow-[0_0_0.5rem_rgba(255,255,255,0.1)] overflow-hidden">

            <!-- Panel close button -->
            <div class="absolute top-0 right-0 p-4 z-60">
                <button type="button" @click="open = false" class="p-2.5 rounded-full flex--center text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600">
                    <x-svg.cross/>
                </button>
            </div>

            <!-- Panel content -->
            <div class="max-h-[80vh] overflow-y-scroll scrollbar-hidden">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
