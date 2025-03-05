@props([
    'label',
    'name',
    'model',
    'asterix' => false
])

<div class="m-0 p-0 max-w-[45rem]">
    <label for="{{ $name }}" class="relative mb-2.5 pl-1 block text-sm-medium text-gray-800 dark:text-gray-200">
        {{ ucfirst($label) }}
        @if($asterix)
            <span class="absolute -top-0.5 ml-0.5 text-rose-500">*</span>
        @endif
    </label>

    <div class="relative flex w-full cursor-pointer appearance-none items-center justify-center rounded-md border-2 border-dashed border-gray-200 p-6 transition-all dark:border-gray-600 dark:bg-gray-800">
        <div class="space-y-1 text-center">
            <div class="mx-auto inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="h-6 w-6 text-gray-500 dark:text-gray-300">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z"/>
                </svg>
            </div>
            <div class="text-gray-600 dark:text-gray-400">
                <span class="font-medium text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-500">
                    Clic pour importer
                </span> ou glisser-déposer
            </div>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                PDF, Word, JPEG, PNG (max. 800mb)
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
    <ul class="my-2 flex flex-col gap-3 font-medium text-red-500 dark:text-red-400">
        @foreach ($errors->get($model) as $error)
            <li class="pl-2 pr-1 text-sm-medium text-red-500 dark:text-red-400">
                {{ $error }}
            </li>
        @endforeach
    </ul>
    @enderror
</div>
