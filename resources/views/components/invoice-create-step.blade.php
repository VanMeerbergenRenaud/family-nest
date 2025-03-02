@props([
    'step',
    'title',
    'description' => null,
])

<div x-show="currentStep === {{ $step }}">
    <h2 class="text-xl-semibold text-slate-800 dark:text-gray-200 mb-2">{{ $title }}</h2>
    <p class="text-sm-regular text-slate-600 dark:text-gray-400 mb-6">{{ $description }}</p>

    <div {{ $attributes }}>
        {{ $slot }}
    </div>
</div>
