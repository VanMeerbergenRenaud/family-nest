<button type="button"
    {{ $attributes->merge(['class' => 'w-full p-2 px-4 flex items-center gap-x-3 text-sm font-medium text-gray-600 bg-white cursor-pointer hover:text-black hover:bg-gray-100']) }}>
    {{ $slot }}
</button>
