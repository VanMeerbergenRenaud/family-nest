@props([
    'name',
    'label',
    'model',
])

<div class="m-0 p-0 max-w-[30rem]">
    <label for="{{ $name }}" class="mb-1.5 pl-1 block text-sm-medium text-gray-800 dark:text-gray-200">
        {{ ucfirst($label) }}
    </label>

    <div class="relative">
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            wire:model.blur="{{ $model }}"
            class="appearance-none m-0 px-3.5 py-2.5 block w-full text-sm-regular text-gray-600 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
            {{ $attributes }}
        >
            {{ $slot }}
        </select>

        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-700 dark:text-gray-300">
            <x-svg.arrow-double />
        </div>
    </div>

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
