@props([
    'label',
    'name',
    'model' => '',
    'asterix' => false
])

<div class="m-0 p-0 max-w-[45rem]" x-data="{ show: false }">
    <label for="{{ $name }}" class="relative mb-1.5 pl-1 block text-sm-medium text-gray-800 dark:text-gray-200">
        {{ ucfirst($label) }}
        @if($asterix)
            <span class="absolute -top-0.5 ml-0.5 text-rose-500">*</span>
        @endif
    </label>

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="password"
        wire:model.blur="{{ $model }}"
        placeholder="••••••••"
        class="m-0 px-3 py-2 block w-full placeholder:text-[0.9375rem] text-gray-600 border border-slate-200 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
        x-bind:type="show ? 'text' : 'password'"
        {{ $attributes }}
    >

    <div class="relative cursor-pointer">
        <svg class="block p-1 scale-200 absolute top-[-1.55rem] right-[0.875rem] w-4 dark:text-gray-400" fill="none" @click="show = !show"
             :class="{'block': !show, 'hidden':show }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
            <path fill="currentColor" d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z" />
        </svg>

        <svg class="hidden p-1 scale-200 absolute top-[-1.55rem] right-[0.875rem] w-4 dark:text-gray-400" fill="none" @click="show = !show"
             :class="{'hidden': !show, 'block':show }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
            <path fill="currentColor" d="M320 400c-75.85 0-137.25-58.71-142.9-133.11L72.2 185.82c-13.79 17.3-26.48 35.59-36.72 55.59a32.35 32.35 0 0 0 0 29.19C89.71 376.41 197.07 448 320 448c26.91 0 52.87-4 77.89-10.46L346 397.39a144.13 144.13 0 0 1-26 2.61zm313.82 58.1l-110.55-85.44a331.25 331.25 0 0 0 81.25-102.07 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64a308.15 308.15 0 0 0-147.32 37.7L45.46 3.37A16 16 0 0 0 23 6.18L3.37 31.45A16 16 0 0 0 6.18 53.9l588.36 454.73a16 16 0 0 0 22.46-2.81l19.64-25.27a16 16 0 0 0-2.82-22.45zm-183.72-142l-39.3-30.38A94.75 94.75 0 0 0 416 256a94.76 94.76 0 0 0-121.31-92.21A47.65 47.65 0 0 1 304 192a46.64 46.64 0 0 1-1.54 10l-73.61-56.89A142.31 142.31 0 0 1 320 112a143.92 143.92 0 0 1 144 144c0 21.63-5.29 41.79-13.9 60.11z" />
        </svg>
    </div>

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
