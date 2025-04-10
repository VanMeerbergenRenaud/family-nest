@props(['plan'])

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <!-- En-tête -->
    <div class="p-6 border-b border-gray-200">
        @if (isset($plan['tag']) && $plan['tag'])
            <div class="flex justify-between items-start mb-1">
                <h3 class="text-lg font-medium text-gray-800">{{ $plan['name'] }}</h3>
                <span class="text-xs text-gray-500 uppercase">{{ $plan['tag'] }}</span>
            </div>
        @else
            <h3 class="text-lg font-medium text-gray-800 mb-1">{{ $plan['name'] }}</h3>
        @endif
        <p class="text-md text-gray-600 mb-3">{{ $plan['description'] }}</p>

        <div class="flex items-baseline mb-6">
            <span class="text-2xl font-bold text-gray-900">{{ $plan['price'] }}</span>
            <span class="text-md text-gray-500 ml-1">/famille/mois</span>
        </div>

        <button class="{{ $plan['button']['class'] }} w-full justify-center">
            {{ $plan['button']['text'] }}
        </button>
    </div>

    <!-- Liste des fonctionnalités -->
    <div class="p-6">
        <ul class="space-y-4">
            @foreach ($plan['features'] as $feature)
                <x-feature-item :available="$feature['available'] ?? true">
                    {{ $feature['text'] ?? $feature }}
                </x-feature-item>
            @endforeach
        </ul>
    </div>
</div>
