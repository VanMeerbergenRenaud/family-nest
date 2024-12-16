@props([
    'label',
    'name',
    'model' => '',
    'placeholder' => '',
])

<div class="m-0 p-0 max-w-[24.375rem]">
    <label for="{{ $name }}" class="mb-2 pl-2 block text-base font-semibold text-gray-800">
        {{ ucfirst($label) }}
    </label>

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        wire:model.blur="{{ $model }}"
        placeholder="{{ $placeholder }}"
        class="my-1.5 px-3 block w-[calc(100%-1.55rem)] font-sans text-sm text-gray-900 resize-none"
        {{ $attributes }}
    ></textarea>

    @error($model)
        <ul class="my-2 flex flex-col gap-3 font-medium text-red-500">
            @foreach ($errors->get($model) as $error)
                <li class="pl-2 text-sm font-medium text-red-500 w-max">
                    {{ $error }}
                </li>
            @endforeach
        </ul>
    @enderror
</div>
