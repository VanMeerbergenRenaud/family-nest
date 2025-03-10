@props([
    'label',
    'name',
    'model',
    'asterix' => false
])

<x-form.field-base :label="$label" :name="$name" :model="$model" :asterix="$asterix">
    <div class="border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
        <div class="flex-center flex-col p-10 min-h-[25rem]">
            <x-svg.download class="w-5 h-5 mb-2"/>
            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                <span
                    class="font-semibold text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-500">
                    Clic pour importer
                </span> ou glisser déposer
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
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
    </div>
</x-form.field-base>
