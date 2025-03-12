@props([
    'name',
    'label',
    'model',
    'asterix' => false,
])

<x-form.field-base :label="$label" :name="$name" :model="$model" :asterix="$asterix">
    <div class="relative @error($name) input-invalid @enderror">
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            wire:model.blur="{{ $model }}"
            class="appearance-none bg-white m-0 px-3.5 py-2.5 block w-full text-sm text-gray-600 border border-slate-200 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
            {{ $attributes }}
        >
            {{ $slot }}
        </select>

        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-700 dark:text-gray-300">
            <x-svg.arrow-double />
        </div>
    </div>
</x-form.field-base>
