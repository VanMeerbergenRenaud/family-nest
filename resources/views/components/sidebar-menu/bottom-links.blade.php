@props([
    'sidebarWide' => null,
])

<ul class="font-medium custom-mt-auto">
    <li>
        <a href="#" class="flex items-center p-2 py-1.5 text-gray-700 rounded-lg dark:text-gray-400 group">
            <x-heroicon-o-arrow-path class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"/>
            @if(!$sidebarWide)
                <span class="lg:hidden ml-3">Rafraîchir</span>
            @else
                <span x-show="{{ $sidebarWide }}" class="ml-3">Rafraîchir</span>
            @endif
        </a>
    </li>
    <li>
        <a href="#" class="flex items-center p-2 py-1.5 text-gray-700 rounded-lg dark:text-gray-400 group">
            <x-heroicon-o-cog class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"/>
            @if(!$sidebarWide)
                <span class="lg:hidden ml-3">Paramètres</span>
            @else
                <span x-show="{{ $sidebarWide }}" class="ml-3">Paramètres</span>
            @endif
        </a>
    </li>
    <li>
        <a href="#" class="flex items-center p-2 py-1.5 text-gray-700 rounded-lg dark:text-gray-400 group">
            <x-heroicon-o-question-mark-circle class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"/>
            @if(!$sidebarWide)
                <span class="lg:hidden ml-3">Centre d'aide</span>
            @else
                <span x-show="{{ $sidebarWide }}" class="ml-3">Centre d'aide</span>
            @endif
        </a>
    </li>
</ul>
