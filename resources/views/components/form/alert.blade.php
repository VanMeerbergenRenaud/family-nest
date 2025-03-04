@props([
    'type' => 'info',
    'title' => '',
    'icon' => true,
])

@php
    // Configuration des couleurs par type
    $config = [
        'success' => [
            'bg' => 'bg-green-50 dark:bg-green-900/20',
            'border' => 'border-green-200 dark:border-green-800',
            'title' => 'text-green-800 dark:text-green-300',
            'text' => 'text-green-700 dark:text-green-200',
            'icon' => 'text-green-400'
        ],
        'warning' => [
            'bg' => 'bg-yellow-50 dark:bg-yellow-900/20',
            'border' => 'border-yellow-200 dark:border-yellow-800',
            'title' => 'text-yellow-800 dark:text-yellow-300',
            'text' => 'text-yellow-700 dark:text-yellow-200',
            'icon' => 'text-yellow-400'
        ],
        'error' => [
            'bg' => 'bg-red-50 dark:bg-red-900/20',
            'border' => 'border-red-200 dark:border-red-800',
            'title' => 'text-red-800 dark:text-red-300',
            'text' => 'text-red-700 dark:text-red-300',
            'icon' => 'text-red-400'
        ],
        'info' => [
            'bg' => 'bg-blue-50 dark:bg-blue-900/20',
            'border' => 'border-blue-200 dark:border-blue-800',
            'title' => 'text-blue-800 dark:text-blue-300',
            'text' => 'text-blue-700 dark:text-blue-200',
            'icon' => 'text-blue-400'
        ]
    ];

    // Utiliser la configuration du type demandé, ou par défaut 'info'
    $settings = $config[$type] ?? $config['info'];
@endphp

<div class="my-4 p-4 {{ $settings['bg'] }} border {{ $settings['border'] }} rounded-lg" {{ $attributes }}>
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
                    @default
                        <x-svg.info class="h-5 w-5 {{ $settings['icon'] }}"/>
                @endswitch
            </div>
        @endif

        <div class="ml-3 flex-grow">
            @if($title)
                <h3 class="text-sm font-medium {{ $settings['title'] }}">
                    {{ $title }}
                </h3>
            @endif

            <div class="mt-2 text-sm {{ $settings['text'] }}">
                {{ $slot }}
            </div>

            @if(isset($actions))
                <div class="mt-4">
                    {{ $actions }}
                </div>
            @endif
        </div>
    </div>
</div>
