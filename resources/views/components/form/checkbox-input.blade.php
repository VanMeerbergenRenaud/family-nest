@props([
    'name',
    'model',
    'label',
    'checked' => false
])

<label for="{{ $name }}" class="relative flex items-center cursor-pointer">
    <input id="{{ $name }}"
           model="{{ $model }}"
           type="checkbox"
           class="h-4 w-4 cursor-pointer transition-all appearance-none rounded border border-slate-300 checked:bg-slate-800 checked:border-slate-800"
        {{ $checked ? 'checked' : '' }}
    />
    <span
        class="absolute text-white peer-checked:opacity-100 top-1/2 left-2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20"
             fill="currentColor" stroke="currentColor" stroke-width="1">
            <path fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"/>
        </svg>
    </span>
    <span class="ml-1.5 text-sm-medium">{{ $label }}</span>
</label>
