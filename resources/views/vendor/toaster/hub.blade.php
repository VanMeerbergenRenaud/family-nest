<div role="status" id="toaster" x-data="toasterHub(@js($toasts), @js($config))" @class([
    'fixed z-50 p-4 w-fit flex flex-col pointer-events-none sm:p-6',
    'bottom-0 right-0' => $alignment->is('bottom'),
    'top-1/2 -translate-y-1/2' => $alignment->is('middle'),
    'top-0 right-0' => $alignment->is('top'),
    'items-start rtl:items-end' => $position->is('left'),
    'items-center' => $position->is('center'),
    'items-end rtl:items-start' => $position->is('right'),
])>
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.isVisible"
             x-init="$nextTick(() => toast.show($el))"
             @if($alignment->is('bottom'))
                 x-transition:enter-start="translate-y-12 opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
             @elseif($alignment->is('top'))
                 x-transition:enter-start="-translate-y-12 opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
             @else
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
             @endif
             x-transition:leave-end="opacity-0 scale-90"
             class="relative duration-300 transform transition ease-in-out w-full pointer-events-auto mb-3"
        >
            <div x-data="{
                parts: toast.message.split('|'),
                getPart(index) { return this.parts.length > index ? this.parts[index] : ''; },
                title() {
                    const titlePart = this.getPart(0);
                    return titlePart.includes('::') ? titlePart.split('::')[0] : titlePart;
                },
                description() {
                    const titlePart = this.getPart(0);
                    return titlePart.includes('::') ? titlePart.split('::')[1] : '';
                },
                getBgColor() {
                    switch(toast.type) {
                        case 'info': return 'bg-gray-50 border-gray-200';
                        case 'success': return 'bg-green-50 border-green-200';
                        case 'warning': return 'bg-amber-50 border-amber-200';
                        case 'error': return 'bg-red-50 border-red-200';
                        default: return 'bg-white border-gray-200';
                    }
                },
                getTextColor() {
                    switch(toast.type) {
                        case 'info': return 'text-gray-700';
                        case 'success': return 'text-green-700';
                        case 'warning': return 'text-amber-700';
                        case 'error': return 'text-red-700';
                        default: return 'text-gray-700';
                    }
                },
                getIconColor() {
                    switch(toast.type) {
                        case 'info': return 'text-gray-500';
                        case 'success': return 'text-green-500';
                        case 'warning': return 'text-amber-500';
                        case 'error': return 'text-red-500';
                        default: return 'text-gray-500';
                    }
                }
            }">
                <!-- Toast container -->
                <div class="max-w-md rounded-xl border px-4 py-3.5 flex items-start cursor-pointer"
                     :class="getBgColor()"
                     @click="toast.dispose()"
                >
                    <!-- Icon based on toast type -->
                    <div class="flex-shrink-0 mr-3">
                        <svg x-show="toast.type === 'info'" class="w-5 h-5" :class="getIconColor()" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                        </svg>
                        <svg x-show="toast.type === 'success'" class="w-5 h-5" :class="getIconColor()" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                        </svg>
                        <svg x-show="toast.type === 'warning'" class="w-5 h-5" :class="getIconColor()" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                        </svg>
                        <svg x-show="toast.type === 'error'" class="w-5 h-5" :class="getIconColor()" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <div class="flex-1 flex flex-col">
                        <div class="flex justify-between items-center">
                            <!-- Title -->
                            <p class="text-sm-medium" :class="getTextColor()" x-text="title()"></p>

                            <!-- Close button -->
                            @if($closeable)
                                <button type="button" class="ml-3">
                                    <svg class="h-4 w-4" :class="getTextColor()" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        <!-- Description (only shown if present) -->
                        <p x-show="description().length > 0" class="text-gray-600 text-sm mt-1" x-text="description()"></p>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
