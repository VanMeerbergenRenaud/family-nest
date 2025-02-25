<button type="button"
    {{ $attributes->merge(['class' => 'w-full px-4 py-2 flex items-center gap-x-3 text-left text-sm-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200']) }}>
    {{ $slot }}
</button>
