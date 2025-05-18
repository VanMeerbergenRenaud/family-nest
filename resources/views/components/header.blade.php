@props([
    'title' => 'Titre par défaut',
    'description' => 'Description par défaut',
    'info' => null,
])

<div {{ $attributes->merge(['class' => 'md:px-4']) }}>
    <h2 class="text-xl-semibold">{{ $title }}</h2>
    <p class="text-sm-regular text-gray-500">{{ $description }}</p>
    <span class="block mt-2 text-sm text-indigo-950 dark:text-pink-500">
        {{ $info }}
    </span>
</div>
