@props([
    'label',
    'name',
    'model' => '',
    'placeholder' => '',
    'asterix' => false,
])

<x-form.field-base :label="$label" :name="$name" :model="$model" :placeholder="$placeholder" :asterix="$asterix">
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="date"
        wire:model.blur="{{ $model }}"
        placeholder="{{ $placeholder }}"
        class="m-0 px-3.5 py-2.5 block w-full appearance-none bg-white text-sm text-gray-600 border border-slate-200 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 @error($name) input-invalid @enderror"
        {{ $attributes }}
    >
</x-form.field-base>
