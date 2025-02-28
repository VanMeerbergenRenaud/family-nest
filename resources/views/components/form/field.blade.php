@props([
    'label',
    'name',
    'type' => 'text',
    'model' => '',
    'placeholder' => '',
])

<div class="m-0 p-0 max-w-[30rem]">
    <label for="{{ $name }}" class="mb-1.5 pl-1 block text-sm-medium text-gray-800 dark:text-gray-200">
        {{ ucfirst($label) }}
    </label>

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        wire:model.blur="{{ $model }}"
        placeholder="{{ $placeholder }}"
        class="m-0 px-3.5 py-2.5 block w-full text-sm-regular text-gray-600 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
        {{ $attributes }}
    >

    @error($model)
    <ul class="my-2 flex flex-col gap-3 font-medium text-red-500 dark:text-red-400">
        @foreach ($errors->get($model) as $error)
            <li class="pl-2 pr-1 text-sm-medium text-red-500 dark:text-red-400">
                {{ $error }}
            </li>
        @endforeach
    </ul>
    @enderror
</div>
