@props(['label', 'alternateBackground' => false])

<div class="{{ $alternateBackground ? 'bg-gray-50 dark:bg-gray-900' : 'bg-white dark:bg-gray-800' }} px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
    <dt class="text-sm-medium text-gray-500 dark:text-gray-300">
        {{ $label }}
    </dt>
    <dd class="mt-1 text-sm-medium text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
        {{ $slot }}
    </dd>
</div>
