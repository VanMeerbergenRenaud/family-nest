@props([
    'invoice' => null,
    'dotsRotation' => false,
])

<x-menu>
    <x-menu.button class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
        <x-svg.dots class="w-5 h-5 text-gray-500 {{ $dotsRotation ? 'rotate-90' : '' }}" />
    </x-menu.button>

    <x-loader.spinner target="toggleFavorite({{ $invoice->id }})" position="fixed" />
    <x-loader.spinner target="archiveInvoice({{ $invoice->id }})" position="fixed" />

    <x-menu.items>
        <x-menu.item wire:click="showInvoiceModal({{ $invoice->id }})" wire:loading.attr="disabled">
            <x-svg.show class="group-hover:text-gray-900"/>
            {{ __('Voir l\'aperçu') }}
        </x-menu.item>

        <x-menu.item type="link" href="{{ route('invoices.show', $invoice->id) }}">
            <x-svg.binocular class="group-hover:text-gray-900"/>
            {{ __('Voir les détails') }}
        </x-menu.item>

        <x-menu.divider/>

        @can('update', $invoice)
            <x-menu.item type="link" href="{{ route('invoices.edit', $invoice->id) }}" wire:navigate>
                <x-svg.edit class="group-hover:text-gray-900"/>
                {{ __('Modifier') }}
            </x-menu.item>

            <x-menu.item wire:click="copyInvoice({{ $invoice->id }})" wire:loading.attr="disabled">
                <x-svg.copy class="group-hover:text-gray-900"/>
                {{ __('Dupliquer') }}
            </x-menu.item>
        @endcan

        <x-menu.item wire:click="downloadInvoice({{ $invoice->id }})" wire:loading.attr="disabled">
            <x-svg.download class="group-hover:text-gray-900"/>
            {{ __('Télécharger') }}
        </x-menu.item>

        @can('addToFavorite', $invoice)
            <x-menu.item wire:click="toggleFavorite({{ $invoice->id }})" wire:loading.attr="disabled">
                @if($invoice->is_favorite)
                    <x-svg.star class="group-hover:text-gray-900 fill-current"/>
                    {{ __('Retirer des favoris') }}
                @else
                    <x-svg.star class="group-hover:text-gray-900"/>
                    {{ __('Ajouter aux favoris') }}
                @endif
            </x-menu.item>
        @endif

        @can('delete', $invoice)
            <x-menu.divider/>

            <x-menu.item wire:click="archiveInvoice({{ $invoice->id }})" class="group hover:text-red-500" wire:loading.attr="disabled">
                <x-svg.archive class="group-hover:text-red-500 stroke-2" />
                {{ __('Archiver') }}
            </x-menu.item>

            <x-menu.item wire:click="showDeleteForm({{ $invoice->id }})" class="group hover:text-red-500" wire:loading.attr="disabled">
                <x-svg.trash class="group-hover:text-red-500"/>
                {{ __('Supprimer') }}
            </x-menu.item>
        @endcan
    </x-menu.items>
</x-menu>
