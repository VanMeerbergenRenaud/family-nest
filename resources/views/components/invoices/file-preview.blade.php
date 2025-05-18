@props([
    'fileInfo' => [],
    'temporaryUrl' => null,
    'storagePath' => null,
    'onRemove' => null,
    'showOcrButton' => false,
    'isOcrProcessing' => false,
    'onOcrProcess' => null
])

<div class="relative w-full h-full flex flex-col">
    @if(isset($onRemove) && $onRemove)
        <div class="absolute top-2.5 right-2.5 z-2">
            <button type="button" wire:click="{{ $onRemove }}" class="p-1">
                <x-svg.cross class="text-red-600 hover:text-black bg-red-300 hover:bg-red-400 rounded-full w-6 h-6 p-1 transition-colors duration-200" />
            </button>
        </div>
    @endif

    <div class="rounded-xl border border-slate-200 min-h-[30rem] flex-center flex-col p-2.5 overflow-y-scroll mb-2">
        {{-- Aperçu pour les images --}}
        @if ($fileInfo['isImage'] ?? false)
            <img src="{{ $temporaryUrl ?? $storagePath ?? '' }}"
                 alt="Aperçu de la facture"
                 class="bg-gray-100 rounded-lg min-h-[60vh]"
                 wire:ignore
                 loading="lazy"
            />
        @elseif ($fileInfo['isPdf'] ?? false)
            @if($temporaryUrl || $storagePath)
                <div wire:ignore class="w-full h-full overflow-hidden rounded-lg">
                    <embed
                        id="pdf-viewer"
                        width="100%"
                        height="100%"
                        class="inline-block w-full h-full min-h-[60vh]"
                        type="application/pdf"
                        src="{{ $temporaryUrl ?? $storagePath }}"
                        loading="lazy"
                    >
                </div>
            @else
                <div class="w-full min-h-[60vh] flex-center flex-col gap-4">
                    <x-svg.pdf class="w-16 h-16 text-gray-400" />
                    <p class="text-center text-sm text-gray-500 px-8 lg:px-12">
                        Le fichier PDF n'est pas disponible pour l'aperçu.
                    </p>
                    <a href="{{ $temporaryUrl ?? $storagePath }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="button-secondary mt-2"
                    >
                        <x-svg.show class="text-white" />
                        Ouvrir le PDF
                    </a>
                </div>
            @endif
        {{-- Aperçu pour les fichiers DOCX --}}
        @elseif ($fileInfo['isDocx'] ?? false)
            <div class="px-4 mb-4 text-center">
                <div class="p-5 text-gray-700 border border-slate-200 rounded-lg bg-slate-100">
                    <p class="mb-2.5 text-md-medium text-slate-700">Aperçu non disponible pour les fichiers Word</p>
                    <p class="text-sm text-slate-500">
                        @if($storagePath)
                            Le fichier a été traité et sauvegardé
                        @else
                            Le fichier sera traité après l'enregistrement de la facture
                        @endif
                    </p>
                </div>
            </div>
        {{-- Pour les fichiers CSV --}}
        @elseif($fileInfo['isCsv'] ?? false)
            <div class="w-24 h-24 mb-5 flex-center bg-green-100 rounded-full">
                <x-svg.csv class="w-12 h-12 text-gray-600" />
            </div>
        {{-- Pour les autres types de fichiers --}}
        @else
            <div class="w-24 h-24 mb-5 flex-center bg-gray-100 rounded-full">
                <x-svg.img class="w-12 h-12 text-gray-600" />
            </div>
        @endif

        {{-- Bouton OCR intégré --}}
        @if($showOcrButton && !$isOcrProcessing && isset($onOcrProcess) && $onOcrProcess)
            <button
                type="button"
                wire:click="{{ $onOcrProcess }}"
                wire:loading.attr="disabled"
                class="mt-3 w-full button-primary justify-center group hover:text-gray-900"
            >
                <x-svg.ocr class="group-hover:stroke-gray-900 group-hover:text-gray-900" />
                Autocompléter automatiquement
            </button>
        @endif
    </div>
</div>
