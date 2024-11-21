@props([
    'label',
    'name',
    'type' => 'text',
    'model' => '',
    'placeholder' => '',
])

<div class="field">

    <label for="{{ $name }}" class="field__label">
        {{ ucfirst($label) }}
    </label>

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        wire:model.blur="{{ $model }}"
        placeholder="{{ $placeholder }}"
        class="field__input"
        {{ $attributes }}
    >

    @error($model)
        <ul class="field-error">
            @foreach ($errors->get($model) as $error)
                <li class="field-error__item">{{ $error }}</li>
            @endforeach
        </ul>
    @enderror
</div>
