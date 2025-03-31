@props([
    'title' => 'Titre par défaut',
    'description' => 'Description par défaut',
])

<div {{ $attributes->merge(['class' => 'px-4']) }}>
    <h2 class="text-xl-semibold">{{ $title }}</h2>
    <p class="text-sm-regular text-gray-500">{{ $description }}</p>
</div>
