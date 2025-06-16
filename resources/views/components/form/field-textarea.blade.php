@props([
    'label',
    'name',
    'model' => '',
    'placeholder' => '',
    'asterix' => false,
])

<x-form.field-base :label="$label" :name="$name" :model="$model" :placeholder="$placeholder" :asterix="$asterix">
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        wire:model.blur="{{ $model }}"
        placeholder="{{ $placeholder }}"
        class="m-0 px-3 py-2 block w-full min-h-32 text-[0.9375rem] resize-none bg-white placeholder:text-[0.9375rem] text-gray-700 border border-slate-200 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 @error($name) input-invalid @enderror"
        {{ $attributes }}
    ></textarea>
</x-form.field-base>
