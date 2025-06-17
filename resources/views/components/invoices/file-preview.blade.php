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
        <div class="absolute top-0 right-0 z-2">
            <button type="button" wire:click="{{ $onRemove }}" class="p-2.5 group">
                <x-svg.cross
                    class="text-red-600 group-hover:text-black bg-red-300 group-hover:bg-red-400 rounded-full w-6 h-6 p-1 transition-colors duration-200"/>
            </button>
        </div>
    @endif

    <div class="rounded-xl border border-slate-200 min-h-[30rem] flex-center flex-col p-2.5 overflow-y-scroll mb-2">
        {{-- Aperçu pour les images --}}
        @if ($fileInfo['isImage'] ?? false)
            <img src="{{ $temporaryUrl ?? $storagePath ?? '' }}"
                 alt="Aperçu de la facture"
                 class="bg-gray-100 rounded-lg min-h-[60vh] object-contain"
                 wire:ignore
                 loading="lazy"
            />
        @elseif ($fileInfo['isPdf'] ?? false)
            <div wire:ignore class="w-full h-full pdf-container">
                <div
                    x-data="{
                        isChromium: false,
                        checkBrowser() {
                            const userAgent = window.navigator.userAgent.toLowerCase();
                            return userAgent.indexOf('chrome') > -1 || userAgent.indexOf('chromium') > -1 || userAgent.indexOf('arc') > -1;
                        },
                        init() {
                            this.isChromium = this.checkBrowser();
                        }
                    }"
                    class="w-full h-full"
                >
                    {{-- Vos templates existants --}}
                    <template x-if="!isChromium && ('{{ $temporaryUrl }}' || '{{ $storagePath }}')">
                        <div class="w-full h-full overflow-hidden rounded-lg">
                            <object
                                id="pdf-viewer"
                                width="100%"
                                height="100%"
                                class="inline-block w-full h-full min-h-[60vh]"
                                type="application/pdf"
                                data="{{ $temporaryUrl ?? $storagePath }}"
                                sandbox="allow-same-origin"
                                loading="lazy"
                            ></object>
                        </div>
                    </template>

                    <template x-if="isChromium || (!('{{ $temporaryUrl }}' || '{{ $storagePath }}'))">
                        <div class="w-full min-h-[60vh] flex-center flex-col gap-4">
                            <x-svg.pdf class="w-12 h-12 text-gray-400"/>
                            <p class="mt-4 flex flex-col gap-2 text-center text-sm text-gray-500 px-8 lg:px-12">
                                <span x-show="isChromium">L'aperçu PDF n'est pas disponible sur le navigateur Google Chrome.</span>
                                <span x-show="!isChromium">Le fichier PDF sera visible après l'enregistrement de la facture.</span>
                            </p>
                            <a href="{{ $temporaryUrl ?? $storagePath }}"
                               class="mt-2 button-primary"
                               target="_blank"
                            >
                                <x-svg.show class=" mr-1"/>
                                Voir le PDF dans un nouvel onglet
                            </a>
                        </div>
                    </template>
                </div>
            </div>
        {{-- Aperçu pour les fichiers DOCX --}}
        @elseif ($fileInfo['isDocx'] ?? false)
            <div class="w-full h-full">
                <div class="w-full min-h-[60vh] flex-center flex-col gap-4">
                    <x-svg.docx class="w-12 h-12 text-gray-400"/>
                    <p class="mt-4 flex flex-col gap-2 text-center text-sm text-gray-500 px-8 lg:px-12">
                        <span>L'aperçu des documents Word (.docx) n'est pas disponible.</span>
                        <span>Cette fonctionnalité sera bientôt accessible, mais il faut encore patienter 😇.</span>
                    </p>
                </div>
            </div>
        {{-- Pour les autres types de fichiers --}}
        @else
            <div class="w-full h-full">
                <div class="w-full min-h-[60vh] flex-center flex-col gap-4">
                    <x-svg.img class="w-12 h-12 text-gray-600"/>
                    <p class="mt-4 flex flex-col gap-2 text-center text-sm text-gray-500 px-8 lg:px-12">
                        <span>L'aperçu de ce type de document n'est pas disponible.</span>
                        <span>Nous n'acceptons que les images, les fichiers PDF et Word.</span>
                    </p>
                </div>
            </div>
        @endif

        {{-- Bouton OCR intégré --}}
        @if($showOcrButton && !$isOcrProcessing && isset($onOcrProcess) && $onOcrProcess && !$fileInfo['isDocx'] && !$fileInfo['isCsv'])
            <button
                type="button"
                wire:click="{{ $onOcrProcess }}"
                wire:loading.attr="disabled"
                class="mt-3 w-full button-primary justify-center group hover:text-gray-900"
            >
                <x-svg.ocr class="group-hover:stroke-gray-900 group-hover:text-gray-900"/>
                Autocompléter automatiquement
            </button>
        @endif
    </div>
</div>
