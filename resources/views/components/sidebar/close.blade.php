<span wire:click="$set('isSidebarOpen', false)"
      aria-label="Fermer la sidebar"
      {{ $attributes }}
>
    {{ $slot }}
</span>
