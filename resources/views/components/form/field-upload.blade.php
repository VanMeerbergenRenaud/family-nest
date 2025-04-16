@props([
    'label',
    'name',
    'model',
    'asterix' => false,
    'title' => 'Traitement en cours',
    'description' => null,
])

<x-form.field-base :label="$label" :name="$name" :model="$model" :asterix="$asterix" class="w-full">
    <div x-data="{ uploading: false, progress: 0 }"
         x-on:livewire-upload-start="uploading = true"
         x-on:livewire-upload-finish="uploading = false"
         x-on:livewire-upload-cancel="uploading = false"
         x-on:livewire-upload-error="uploading = false"
         x-on:livewire-upload-progress="progress = $event.detail.progress"
         class="@error($name) input-invalid @enderror relative border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-slate-50 dark:bg-gray-700 hover:bg-slate-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 mx-1"
    >
        <div class="flex-center flex-col p-10 min-h-[25rem]">
            <x-svg.download class="w-5 h-5 mb-5"/>

            <p class="mb-4">
                <span class="text-md-medium text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-500">
                    Clic pour importer
                </span>
                <span class="block text-sm text-center text-gray-500 dark:text-gray-400">
                    ou glisser déposer
                </span>
            </p>

            <p class="text-xs text-center max-w-48 text-gray-500 dark:text-gray-400">
                PDF, DOCX, PNG, JPG ou JPEG (MAX. 10 Mo)
            </p>

            <div class="mt-3 flex flex-wrap justify-center gap-2">
                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">PDF</span>
                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">DOCX</span>
                <span class="px-2 py-1 bg-green-200 text-green-800 text-xs rounded-full">IMG</span>
            </div>
        </div>

        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="file"
            wire:model.blur="{{ $model }}"
            accept=".pdf,.docx,.jpeg,.jpg,.png"
            class="absolute top-0 left-0 h-full w-full cursor-pointer opacity-0"
            {{ $attributes }}
        >

        <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 max-w-md w-80 rounded-xl overflow-hidden border border-transparent">
            <div wire:loading
                 wire:target="{{ $model }}"
                 style="display: none;"
                 class="w-full"
            >
                <div class="relative z-20 bg-white rounded-xl border border-slate-200 w-full h-full">
                    <div class="flex items-center px-5 py-3 gap-5 w-full">

                        {{-- Spinner --}}
                        <div class="relative flex-shrink-0">
                            <x-svg.spinner class="h-5 w-5 text-blue-500" />
                        </div>

                        {{-- Title and description --}}
                        <div class="flex-grow">
                            <div class="font-medium text-gray-800">{{ $title }}</div>

                            @if($description)
                                <div class="text-sm text-gray-500 mt-1 leading-snug">
                                    {{ $description }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div x-show="uploading" class="w-full px-4 mb-2">
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div x-bind:style="'width: ' + progress + '%'"
                                 class="bg-blue-500 h-full rounded-full transition-all duration-300 ease-out"
                            ></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1 text-right" x-text="progress + '%'"></div>
                    </div>

                    {{-- Close button --}}
                    <button
                        type="button"
                        @click="$wire.set('{{ $model }}', null)"
                        class="absolute top-0 right-0.5 p-3 z-10 cursor-pointer"
                        aria-label="Fermer le dialogue de chargement"
                    >
                        <x-svg.cross class="w-4 h-4"/>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-form.field-base>
