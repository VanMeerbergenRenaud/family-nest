@props ([
    'vertical' => false,
])

@if ($vertical)
    <div aria-orientation="vertical" role="separator" {{ $attributes->merge(['class' => 'w-[1px] h-full bg-gray-200 dark:bg-gray-700']) }}></div>
@else
    <div aria-orientation="horizontal" role="separator" {{ $attributes->merge(['class' => 'h-[1px] w-full bg-gray-200 dark:bg-gray-700']) }}></div>
@endif
