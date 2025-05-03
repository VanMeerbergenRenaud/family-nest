@props([
    'showInvoicePreviewModal' => false,
    'filePath' => $filePath,
    'fileExtension' => $fileExtension,
    'fileName' => $fileName,
])

<div>
    @if($showInvoicePreviewModal)
        <x-modal wire:model="showInvoicePreviewModal">
            <x-modal.panel class="max-w-4xl">
                <p class="sticky top-0 p-5 px-8 max-w-full text-xl-bold bg-white dark:bg-gray-800 dark:border-gray-700 z-20">
                    {{ __('Aperçu de la facture') }}
                </p>

                <div class="p-1 border-b border-gray-200 bg-gray-50 dark:bg-gray-900 relative overflow-auto">
                    <div wire:loading.remove>
                        <x-invoices.file-viewer
                            :filePath="$filePath"
                            :fileExtension="$fileExtension"
                            :fileName="$fileName"
                            class="min-h-[60vh] max-h-[75vh]"
                        />
                    </div>
                </div>

                <x-modal.footer class="bg-white dark:bg-gray-800 border-t border-gray-400 dark:border-gray-700">
                    <div class="flex justify-between w-full">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-medium">{{ __('Type') }}:</span>
                            {{ strtoupper($fileExtension) ?? __('Inconnu') }}
                        </p>
                        <x-modal.close>
                            <button type="button" class="button-secondary">
                                {{ __('Fermer') }}
                            </button>
                        </x-modal.close>
                    </div>
                </x-modal.footer>
            </x-modal.panel>
        </x-modal>
    @endif
</div>
