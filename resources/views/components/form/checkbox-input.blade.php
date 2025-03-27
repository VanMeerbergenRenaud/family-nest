@props([
    'name',
    'model',
    'label',
    'checked' => false
])

<label for="{{ $name }}" class="relative w-full flex items-center cursor-pointer">
    <input id="{{ $name }}"
           model="{{ $model }}"
           type="checkbox"
           class="h-4 w-4 cursor-pointer transition-all appearance-none rounded bg-white border border-slate-400 checked:bg-slate-800 checked:border-slate-800 dark:checked:bg-gray-300 dark:border-slate-400"
        {{ $checked ? 'checked' : '' }}
        {{ $attributes }}
    />
    <span class="absolute text-white peer-checked:opacity-100 top-1/2 left-2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none">
        <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="white">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
        </svg>
    </span>
    <span class="ml-2 text-sm-medium">{{ $label }}</span>
</label>
