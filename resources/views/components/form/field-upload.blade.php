@props([
    'label',
    'name',
    'model',
    'asterix' => false
])

<x-form.field-base :label="$label" :name="$name" :model="$model" :asterix="$asterix" class="w-full px-2">
    <div x-data="{ uploading: false, progress: 0 }"
         x-on:livewire-upload-start="uploading = true"
         x-on:livewire-upload-finish="uploading = false"
         x-on:livewire-upload-cancel="uploading = false"
         x-on:livewire-upload-error="uploading = false"
         x-on:livewire-upload-progress="progress = $event.detail.progress"
        class="border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-slate-50 dark:bg-gray-700 hover:bg-slate-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600"
    >
        <div class="flex-center flex-col p-10 min-h-[25rem]">
            <x-svg.download class="w-5 h-5 mb-5"/>
            <p class="mb-3">
                <span class="text-md-medium text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-500">
                    Clic pour importer
                </span>
                <span class="block text-sm text-center text-gray-500 dark:text-gray-400">
                    ou glisser déposer
                </span>
            </p>
            <p class="text-xs text-center text-gray-500 dark:text-gray-400">
                SVG, PNG, JPG or GIF (MAX. 800x400px)
            </p>
        </div>
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="file"
            wire:model.blur="{{ $model }}"
            class="absolute top-0 left-0 h-full w-full cursor-pointer opacity-0"
            {{ $attributes }}
        >

        <div class="fixed w-fit top-0 right-0 p-6">
            <div wire:loading wire:target="{{ $model }}" class="bg-gray-100 px-4 py-2.5">
                <span class="text-sm-medium">Importation en cours...</span>

                <div x-show="uploading">
                    <progress max="100" x-bind:value="progress"></progress>
                </div>
            </div>
        </div>
    </div>
</x-form.field-base>
