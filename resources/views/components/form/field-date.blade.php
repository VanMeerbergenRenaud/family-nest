@props([
    'label',
    'name',
    'model' => '',
    'placeholder' => '',
    'asterix' => false
])

<div class="m-0 p-0 max-w-[45rem]">
    <label for="{{ $name }}" class="relative mb-1.5 pl-1 block text-sm-medium text-gray-800 dark:text-gray-200">
        {{ ucfirst($label) }}
        @if($asterix)
            <span class="absolute -top-0.5 ml-0.5 text-rose-500">*</span>
        @endif
    </label>

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="date"
        wire:model.blur="{{ $model }}"
        placeholder="{{ $placeholder }}"
        class="m-0 px-3.5 py-2.5 block w-full appearance-none bg-white text-sm text-gray-600 border border-slate-200 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
        {{ $attributes }}
    >

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
