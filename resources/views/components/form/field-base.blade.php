@props([
    'label',
    'name',
    'model' => '',
    'placeholder' => '',
    'asterix' => false,
])

<div {{ $attributes->merge(['class' => 'm-0 p-0 max-w-[45rem]']) }}>
    <label for="{{ $name }}" class="relative mb-1.5 pl-2 block text-sm-medium text-gray-800 dark:text-gray-200">
        {{ ucfirst($label) }}
        @if($asterix)
            <span class="absolute -top-0.5 ml-0.5 text-rose-500">*</span>
        @endif
    </label>

    {{ $slot }}

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
