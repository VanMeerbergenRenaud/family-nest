@props([
    'type' => 'info',
    'title' => '',
    'icon' => true,
    'layout' => 'default',
])

@php
    $config = [
        'success' => [
            'bg' => 'bg-green-50 dark:bg-green-900/20',
            'border' => 'border-green-300 dark:border-green-800',
            'title' => 'text-green-600 dark:text-green-300',
            'text' => 'text-green-800 dark:text-green-200',
            'icon' => 'text-green-400'
        ],
        'warning' => [
            'bg' => 'bg-yellow-50 dark:bg-yellow-900/20',
            'border' => 'border-yellow-300 dark:border-yellow-800',
            'title' => 'text-yellow-600 dark:text-yellow-300',
            'text' => 'text-yellow-800 dark:text-yellow-200',
            'icon' => 'text-yellow-400'
        ],
        'error' => [
            'bg' => 'bg-red-50 dark:bg-red-900/20',
            'border' => 'border-red-300 dark:border-red-800',
            'title' => 'text-red-600 dark:text-red-300',
            'text' => 'text-red-800 dark:text-red-300',
            'icon' => 'text-red-400'
        ],
        'info' => [
            'bg' => 'bg-blue-50 dark:bg-blue-900/20',
            'border' => 'border-blue-300 dark:border-blue-800',
            'title' => 'text-blue-600 dark:text-blue-300',
            'text' => 'text-blue-800 dark:text-blue-200',
            'icon' => 'text-blue-400'
        ],
        'inProgress' => [
            'bg' => 'bg-orange-50 dark:bg-orange-900/20',
            'border' => 'border-orange-300 dark:border-orange-800',
            'title' => 'text-orange-600 dark:text-orange-300',
            'text' => 'text-orange-800 dark:text-orange-300',
            'icon' => 'text-orange-400'
        ],
    ];

    $settings = $config[$type] ?? $config['info'];
@endphp

<div class="p-4 w-full h-fit {{ $settings['bg'] }} border {{ $settings['border'] }} rounded-lg" {{ $attributes }}>

    {{-- Layout "iconLeft" : icône à gauche, titre et contenu à droite --}}
    @if($layout === 'iconLeft')
        <div class="flex">
            @if($icon)
                <div class="flex-shrink-0">
                    @switch($type)
                        @case('success')
                            <x-svg.success class="h-5 w-5 {{ $settings['icon'] }}"/>
                            @break
                        @case('warning')
                            <x-svg.warning class="h-5 w-5 {{ $settings['icon'] }}"/>
                            @break
                        @case('error')
                            <x-svg.error class="h-5 w-5 {{ $settings['icon'] }}"/>
                            @break
                        @case('inProgress')
                            <x-svg.in-progress class="h-4 w-4 {{ $settings['icon'] }}"/>
                            @break
                        @default
                            <x-svg.info class="h-4 w-4 {{ $settings['icon'] }}"/>
                    @endswitch
                </div>
            @endif

            <div class="ml-3 flex-grow">
                @if($title)
                    <h4 role="heading" aria-level="4" class="text-sm-medium {{ $settings['title'] }}">
                        {{ $title }}
                    </h4>
                @endif

                <div class="mt-2 text-sm {{ $settings['text'] }}">
                    {{ $slot }}
                </div>
            </div>
        </div>
    @else
        {{-- Layout "default" : icône et titre alignés, contenu en dessous --}}
        <div class="flex items-center">
            @if($icon)
                <div class="flex-shrink-0 mr-2">
                    @switch($type)
                        @case('success')
                            <x-svg.success class="h-5 w-5 {{ $settings['icon'] }}"/>
                            @break
                        @case('warning')
                            <x-svg.warning class="h-5 w-5 {{ $settings['icon'] }}"/>
                            @break
                        @case('error')
                            <x-svg.error class="h-5 w-5 {{ $settings['icon'] }}"/>
                            @break
                        @case('inProgress')
                            <x-svg.in-progress class="h-4 w-4 {{ $settings['icon'] }}"/>
                            @break
                        @default
                            <x-svg.info class="h-4 w-4 {{ $settings['icon'] }}"/>
                    @endswitch
                </div>
            @endif

            @if($title)
                <p class="text-sm-medium {{ $settings['title'] }}">
                    {{ $title }}
                </p>
            @endif
        </div>

        <div class="mt-2.5 text-sm {{ $settings['text'] }}">
            {{ $slot }}
        </div>
    @endif

    @if(isset($actions))
        <div class="mt-4">
            {{ $actions }}
        </div>
    @endif
</div>
