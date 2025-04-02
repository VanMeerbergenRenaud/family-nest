{{-- loader.blade.php --}}
@props([
    'target' => null,
    'title' => 'Traitement en cours',
    'description' => null
])

<div wire:loading.flex wire:target="{{ $target }}"
     class="fixed bottom-0 left-1/2 -translate-x-1/2 z-50 max-w-md w-[calc(100%-3rem)] p-[1.5px] rounded-xl overflow-hidden border border-transparent"
     style="display: none;"
>
    <div class="animate-rotate absolute inset-0 h-full w-full rounded-full bg-[conic-gradient(from_0deg_at_50%_50%,#5096FF,#5096FF_40deg,transparent_120deg,transparent_360deg)] z-10"></div>

    <div class="relative z-20 bg-white rounded-[10px] border border-slate-200 w-full h-full">
        <div class="flex items-center px-5 py-3 gap-5">

            {{-- Spinner --}}
            <div class="relative flex-shrink-0">
                <x-svg.spinner class="h-5 w-5 text-blue-500" />
            </div>

            {{-- Title and description --}}
            <div class="flex-grow"> {{-- Pas besoin de z-index ici car déjà dans le conteneur z-20 --}}
                <div class="font-medium text-gray-800">{{ $title }}</div>

                @if($description)
                    <div class="text-sm text-gray-500 mt-1 leading-snug">
                        {{ $description }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Close button --}}
        <button
            type="button"
            @click="$wire.set('{{ $target }}', null)"
            class="absolute top-0 right-0.5 p-3 z-10 cursor-pointer"
            aria-label="Fermer le dialogue de chargement"
        >
            <x-svg.cross class="w-4 h-4"/>
        </button>
    </div>
</div>
