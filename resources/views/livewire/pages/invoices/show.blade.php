<div class="flex flex-col gap-6 p-4">

    <div class="max-sm:grid flex justify-between items-center gap-3">
        <h2 role="heading" aria-level="2" class="px-4 text-2xl font-bold text-gray-900">
            Détails de la facture
        </h2>

        <div class="flex-center flex-wrap gap-3">
            @can('addToFavorite', $invoice)
                <button type="button" wire:click="toggleFavorite({{ $invoice->id }})" class="button-primary" wire:loading.attr="disabled">
                    @if($invoice->is_favorite)
                        <x-svg.star class="group-hover:text-gray-900 fill-current"/>
                        {{ __('Retirer des favoris') }}
                    @else
                        <x-svg.star class="group-hover:text-gray-900"/>
                        {{ __('Ajouter aux favoris') }}
                    @endif
                </button>
            @endif

            @can('update', $invoice)
                <a href="{{ route('invoices.edit', $invoice->id) }}" class="button-primary" wire:navigate>
                    <x-svg.edit class="group-hover:text-gray-900"/>
                    {{ __('Modifier') }}
                </a>

                <button type="button" wire:click="copyInvoice({{ $invoice->id }})" class="button-primary" wire:loading.attr="disabled">
                    <x-svg.copy class="group-hover:text-gray-900"/>
                    {{ __('Dupliquer') }}
                </button>
            @endcan

            <button type="button" wire:click="downloadInvoice({{ $invoice->id }})" class="button-primary" wire:loading.attr="disabled">
                <x-svg.download class="group-hover:text-gray-900"/>
                {{ __('Télécharger') }}
            </button>

            @can('delete', $invoice)
                @if($invoice->is_archived)
                    <button type="button" wire:click="restoreInvoice({{ $invoice->id }})" class="button-primary" wire:loading.attr="disabled">
                        <x-svg.restore />
                        {{ __('Restaurer') }}
                    </button>
                @else
                    <button type="button" wire:click="archiveInvoice({{ $invoice->id }})" class="button-danger" wire:loading.attr="disabled">
                        <x-svg.archive class="text-white stroke-2" />
                        {{ __('Archiver') }}
                    </button>
                @endif

                <button type="button" wire:click="showDeleteForm({{ $invoice->id }})" class="button-danger" wire:loading.attr="disabled">
                    <x-svg.trash class="text-white"/>
                    {{ __('Supprimer') }}
                </button>
            @endcan

        </div>
    </div>

    <div class="lg:grid lg:grid-cols-2 lg:gap-4">
        <div class="relative flex-center w-full h-full max-h-[90vh] overflow-hidden border border-slate-200 bg-gray-100 rounded-xl">
            <x-invoices.file-viewer
                :$filePath
                :$fileExtension
                :$fileName
                class="w-full h-full min-h-[50vh]"
            />
        </div>
        <x-invoices.form.summary :$form :$family_members />
    </div>
</div>
