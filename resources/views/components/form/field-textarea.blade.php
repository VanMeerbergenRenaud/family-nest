@props([
    'label',
    'name',
    'model' => '',
    'placeholder' => '',
])

<div class="m-0 p-0 max-w-[30rem]">
    <label for="{{ $name }}" class="mb-1.5 pl-1 block text-sm-medium text-gray-800">
        {{ ucfirst($label) }}
    </label>

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        wire:model.blur="{{ $model }}"
        placeholder="{{ $placeholder }}"
        class="m-0 px-3 py-2 block w-[calc(100%-1.55rem)] resize-none text-sm-regular text-gray-600 border-gray-300 rounded-md"
        {{ $attributes }}
    ></textarea>

    @error($model)
        <ul class="my-2 flex flex-col gap-3 font-medium text-red-500">
            @foreach ($errors->get($model) as $error)
                <li class="pl-2 pr-1 text-sm-medium text-red-500">
                    {{ $error }}
                </li>
            @endforeach
        </ul>
    @enderror
</div>
