@props([
    'invoice',
    'dotsRotation' => false,
])

<x-menu>
    <x-menu.button class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                   x-bind:class="{ 'bg-gray-100 dark:bg-gray-700': menuOpen }"
    >
        <x-svg.dots class="w-5 h-5 text-gray-500 {{ $dotsRotation ? 'rotate-90' : '' }}" />
    </x-menu.button>

    <x-menu.items>
        <x-menu.item wire:click="showInvoiceModal({{ $invoice->id }})">
            <x-svg.show class="w-4 h-4 group-hover:text-gray-900"/>
            {{ __('Voir l‘aperçu') }}
        </x-menu.item>

        <x-menu.item wire:click="showInvoicePage({{ $invoice->id }})">
            <x-svg.binocular class="w-4 h-4 group-hover:text-gray-900"/>
            {{ __('Voir en détail') }}
        </x-menu.item>

        <x-menu.divider/>

        <x-menu.item wire:click="toggleFavorite({{ $invoice->id }})">
            <x-svg.star class="w-4 h-4 group-hover:text-gray-900"/>
            @if($invoice->is_favorite)
                {{ __('Retirer des favoris') }}
            @else
                {{ __('Ajouter aux favoris') }}
            @endif
        </x-menu.item>

        <x-menu.item wire:click="downloadInvoice({{ $invoice->id }})">
            <x-svg.download class="w-4 h-4 group-hover:text-gray-900"/>
            {{ __('Télécharger') }}
        </x-menu.item>

        <x-menu.item wire:click="showEditPage({{ $invoice->id }})">
            <x-svg.edit class="w-4 h-4 group-hover:text-gray-900"/>
            {{ __('Modifier') }}
        </x-menu.item>

        <x-menu.item wire:click="showArchiveForm({{ $invoice->id }})" class="group hover:text-red-500">
            <x-svg.trash class="w-4 h-4 group-hover:text-red-500"/>
            {{ __('Archiver') }}
        </x-menu.item>
    </x-menu.items>
</x-menu>
