@props([
    'badgeText' => '',
    'badgeIcon' => '',
    'badgeClasses' => 'bg-white text-gray-700',
    'title' => '',
    'description' => '',
])

<div {{ $attributes->merge(['class' => 'mx-auto flex max-w-2xl flex-col items-center gap-y-4 text-center']) }}>
    @if($badgeText)
        <p @class([
            'inline-flex items-center py-1 pl-4 pr-3.5 gap-2.5 text-sm-medium rounded-full border border-gray-200',
            $badgeClasses
        ])>
            @if($badgeIcon)
                <span class="text-sm-medium">{{ $badgeIcon }}</span>
            @endif
            {{ $badgeText }}
        </p>
    @endif

    @if($title)
        <h2 role="heading" aria-level="2" class="homepage-title">
            {!! $title !!}
        </h2>
    @endif

    @if($description)
        <p class="text-lg leading-8 text-gray-600 text-balance">
            {{ $description }}
        </p>
    @endif

    @if($slot->isNotEmpty())
        <div class="mt-2">
            {{ $slot }}
        </div>
    @endif
</div>
