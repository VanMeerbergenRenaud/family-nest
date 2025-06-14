@props([
    'imagePosition' => 'left',
    'imageSrc' => '',
    'badgeText' => '',
    'badgeIcon' => '',
    'badgeClasses' => 'bg-white text-gray-700',
    'title' => '',
    'description' => '',
    'url' => '#',
])

<div class="grid grid-cols-1 items-center gap-x-16 gap-y-16 lg:grid-cols-2">
    {{-- Colonne de l'image (code inchangé) --}}
    <div @class([
        'flex-center rounded-xl bg-gray-50/80 md:p-8',
        'lg:order-last' => $imagePosition === 'right',
    ])>
        <img
            src="{{ $imageSrc }}"
            alt="Illustration de la fonctionnalité : {{ $title }}"
            class="w-full rounded-xl bg-white"
            width="2432"
            height="1442"
        >
    </div>

    {{-- Colonne de texte --}}
    <div class="flex justify-center">
        <div class="flex max-w-lg flex-col items-start gap-y-6">
            <p @class([
            'inline-flex items-center py-1 pl-4 pr-3.5 gap-2.5 text-sm-medium rounded-full border border-gray-200',
            $badgeClasses
        ])>
                @if($badgeIcon)
                    <span class="text-sm-medium">{{ $badgeIcon }}</span>
                @endif
                {{ $badgeText }}
            </p>

            <h2 class="homepage-title">
                {{ $title }}
            </h2>

            <p class="homepage-text">
                {{ $description }}
            </p>

            <a href="{{ $url }}" class="button-primary w-fit">
                En savoir plus
                <x-svg.arrows.right />
            </a>
        </div>
    </div>
</div>
