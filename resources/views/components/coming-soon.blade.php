@props([
    'title' => 'Fonctionnalité à venir',
])

<div class="flex-center flex-col min-h-[80vh] px-4">
    <!-- Badge avec animation subtile -->
    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm-medium bg-gray-50 text-gray-600 border border-gray-200">
        {{ __('En développement') }}
    </span>

    <!-- Titre avec espacement optimisé -->
    <h2 class="mt-6 text-center display-sm-bold tracking-tight text-gray-900 dark:text-white">
        {{ $title }}
    </h2>

    <!-- Sous-titre avec espacement amélioré -->
    <p class="mt-3 text-center text-lg-medium text-gray-700">
        {{ __('Bientôt disponible') }}
    </p>

    <!-- Description avec meilleure lisibilité -->
    <p class="mt-4 text-md text-gray-500 text-center max-w-md mx-auto leading-relaxed">
        {{ __('Cette fonctionnalité est en cours de développement et sera disponible prochainement.') }}
    </p>

    <!-- Indicateur de progression avec animation subtile -->
    <div class="flex justify-center space-x-2 mt-8">
        <span class="w-2 h-2 bg-gray-300 rounded-full animate-pulse"></span>
        <span class="w-2 h-2 bg-gray-400 rounded-full animate-pulse delay-75"></span>
        <span class="w-2 h-2 bg-gray-500 rounded-full animate-pulse delay-150"></span>
    </div>
</div>
