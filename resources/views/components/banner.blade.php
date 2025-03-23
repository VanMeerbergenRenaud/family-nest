{{--
    Composant: x-edit-mode-banner
    Description: Bannière minimaliste indiquant le mode édition (pour insertion dans le header)
    Usage: <x-edit-mode-banner />
--}}

@props([
    'message' => 'Message de bannière',
    'showCloseButton' => true
])

<div class="bg-gray-200 text-gray-800 text-center py-2 w-full"
     x-data="{ show: true }"
     x-show="show"
     x-transition.opacity>
    <span class="text-sm-medium">{{ $message }}</span>

    @if($showCloseButton)
        <button @click="show = false"
                class="absolute right-5 top-3 text-white hover:text-gray-300 transition-colors"
                aria-label="Fermer la notification">
            <x-svg.cross class="text-gray-800" />
        </button>
    @endif
</div>
