@props([
     'title' => 'Chargement en cours...',
    'description' => 'Veuillez patienter pendant que nous traitons votre demande.',
])

<div
    wire:loading.flex
    wire:target="processOcr"
    class="fixed inset-0 z-20 flex-center backdrop-blur-[2px] bg-[rgba(0,0,0,0.25)]"
>
    <div class="relative mx-4 overflow-hidden bg-white rounded-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-start mb-6">
                <div class="relative pt-1 mr-4">
                    <svg class="w-6 h-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>

                <div>
                    <h3 class="text-lg-medium text-slate-800 dark:text-white mb-2">{{ $title }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
                </div>
            </div>

            <div class="relative w-full h-1 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="absolute h-full w-full flex">
                    <div class="h-full w-1/3 bg-gradient-to-r from-indigo-400 to-indigo-600 opacity-80 rounded-full animate-progress-1"></div>
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-slate-500 dark:text-slate-400">
                Ceci peut prendre jusqu'à 60 secondes maximum...
            </div>
        </div>
    </div>
</div>

@livewireStyles
<style>
    @keyframes progress-1 {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(200%); }
    }

    .animate-progress-1 {
        animation: progress-1 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }
</style>
@livewireStyles
