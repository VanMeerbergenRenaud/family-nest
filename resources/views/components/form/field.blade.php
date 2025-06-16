@props([
    'label',
    'name',
    'type' => 'text',
    'model' => '',
    'placeholder' => '',
    'asterix' => false,
])

<x-form.field-base :label="$label" :name="$name" :model="$model" :placeholder="$placeholder" :asterix="$asterix">
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        wire:model.blur="{{ $model }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'w-full pl-3 pr-2 py-2 placeholder:text-[0.9375rem] text-[0.9375rem] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200' . ($errors->has($name) ? ' input-invalid' : ''),
        ]) }}
    >
</x-form.field-base>
