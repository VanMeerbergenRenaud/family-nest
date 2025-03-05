@props([
    'label',
    'name',
    'model',
    'asterix' => false
])

<div class="m-0 p-0 max-w-[45rem]">
    <label for="{{ $name }}" class="relative mb-1.5 pl-1 block text-sm-medium text-gray-800 dark:text-gray-200">
        {{ ucfirst($label) }}
        @if($asterix)
            <span class="absolute -top-0.5 ml-0.5 text-rose-500">*</span>
        @endif
    </label>

    <div class="border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
        <div class="flex-center flex-col p-10 min-h-[25rem]">
            <x-svg.download class="w-5 h-5 mb-2" />
            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                <span class="font-semibold text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-500">
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
        >
    </div>

    @error($model)
        <ul class="my-2 flex flex-col gap-2 font-medium text-red-500 dark:text-red-400">
            @foreach ($errors->get($model) as $error)
                <li class="pl-2 pr-1 text-sm-medium text-red-500 dark:text-red-400">
                    {{ $error }}
                </li>
            @endforeach
        </ul>
    @enderror
</div>
