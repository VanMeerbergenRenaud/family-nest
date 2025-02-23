@props([
    'key' => '',
])

<kbd {{ $attributes->merge(['class' => 'pointer-events-none flex items-center gap-1.5 rounded-md border border-gray-200 bg-gray-50 px-2 py-0.5 font-sans text-xs font-medium text-gray-500']) }}>
    {{ $key }}
</kbd>
