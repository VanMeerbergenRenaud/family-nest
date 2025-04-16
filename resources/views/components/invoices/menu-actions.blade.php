@props([
    'invoice' => null,
    'dotsRotation' => false,
])

<x-menu>
    <x-menu.button class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
        <x-svg.dots class="w-5 h-5 text-gray-500 {{ $dotsRotation ? 'rotate-90' : '' }}" />
    </x-menu.button>

    <x-menu.items>
        <!-- Actions de visualisation -->
        <x-menu.item wire:click="showInvoiceModal({{ $invoice->id }})">
            <x-svg.show class="group-hover:text-gray-900"/>
            {{ __('Voir l\'aperçu') }}
        </x-menu.item>

        <x-menu.item wire:click="showSidebarInvoice({{ $invoice->id }})">
            <x-svg.binocular class="group-hover:text-gray-900"/>
            {{ __('Voir les détails') }}
        </x-menu.item>

        <x-menu.divider/>

        <!-- Actions d'édition -->
        <x-menu.item type="link" href="{{ route('invoices.edit', $invoice->id) }}">
            <x-svg.edit class="group-hover:text-gray-900"/>
            {{ __('Modifier') }}
        </x-menu.item>

        <x-menu.item wire:click="copyInvoice({{ $invoice->id }})">
            <x-svg.copy class="group-hover:text-gray-900"/>
            {{ __('Dupliquer') }}
        </x-menu.item>

        <!-- Actions secondaires -->
        <x-menu.item wire:click="downloadInvoice({{ $invoice->id }})">
            <x-svg.download class="group-hover:text-gray-900"/>
            {{ __('Télécharger') }}
        </x-menu.item>

        <x-menu.item wire:click="toggleFavorite({{ $invoice->id }})">
            @if($invoice->is_favorite)
                <x-svg.star class="group-hover:text-gray-900 fill-current"/>
                {{ __('Retirer des favoris') }}
            @else
                <x-svg.star class="group-hover:text-gray-900"/>
                {{ __('Ajouter aux favoris') }}
            @endif
        </x-menu.item>

        <x-menu.divider/>

        <!-- Action destructive -->
        <x-menu.item wire:click="archiveInvoice({{ $invoice->id }})" class="group hover:text-red-500">
            <x-svg.archive class="group-hover:text-red-500"/>
            {{ __('Archiver') }}
        </x-menu.item>
    </x-menu.items>
</x-menu>
