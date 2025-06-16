@props([
    'title' => 'Titre par défaut',
    'description' => 'Description par défaut',
])

<div class="bg-white px-5.5 py-4 rounded-xl border border-slate-200 w-fit max-w-[32rem] h-fit">
    <h2 class="text-lg-semibold text-gray-800 dark:text-gray-200">
        {{ $title }}
    </h2>
    <p class="text-md-regular text-gray-500 mt-2 mb-4.5 pr-2">
        {{ $description }}
    </p>
    <div class="flex flex-wrap items-center gap-3">
        {{ $slot }}
    </div>
</div>
