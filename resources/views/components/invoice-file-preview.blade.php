@props([
    'fileInfo' => [],
    'temporaryUrl' => null,
    'storagePath' => null,
    'onRemove' => null
])

<div class="relative w-full h-full">
    @if(isset($onRemove) && $onRemove)
        <button type="button"
                wire:click="{{ $onRemove }}"
                class="absolute top-2.5 right-2.5 z-2"
        >
            <x-svg.cross class="text-red-600 hover:text-black bg-red-300 hover:bg-red-400 rounded-full w-6 h-6 p-1 transition-colors duration-200" />
        </button>
    @endif

    <div class="rounded-xl border border-slate-200 min-h-[30rem] flex flex-col items-center justify-center p-2 overflow-y-scroll">
        {{-- Aperçu pour les images --}}
        @if ($fileInfo['isImage'] ?? false)
            <img src="{{ $temporaryUrl ?? $storagePath ?? '' }}"
                 alt="Aperçu de la facture"
                 class="bg-gray-100 rounded-xl max-h-[50vh]"
            />
            {{-- Preview pour les fichiers PDF --}}
        @elseif ($fileInfo['isPdf'] ?? false)
            @if($storagePath)
                <div class="w-full h-[40vh] overflow-hidden rounded-xl">
                    <iframe src="{{ $storagePath }}"
                            width="100%"
                            height="100%"
                            class="rounded-xl"
                            style="border: none;"
                    ></iframe>
                </div>
            @else
                <div class="px-4 mb-4 text-center">
                    <div class="p-5 text-gray-700 text-md-medium border border-slate-200 rounded-xl bg-slate-100">
                        <p class="mb-2.5 font-medium text-slate-700">Aperçu non disponible pour les fichiers PDF</p>
                        <p class="text-sm text-slate-500">Le fichier sera traité après l'enregistrement de la facture.</p>
                    </div>
                </div>
            @endif
            {{-- Pour les fichiers Word --}}
        @elseif ($fileInfo['isDocx'] ?? false)
            <div class="px-4 mb-4 text-center">
                <div class="p-5 text-gray-700 text-md-medium border border-slate-200 rounded-xl bg-slate-100">
                    <p class="mb-2.5 font-medium text-slate-700">Aperçu non disponible pour les fichiers Word</p>
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
            {{-- Icône générique pour les autres types de fichiers --}}
        @else
            <div class="w-24 h-24 mb-5 flex-center bg-gray-100 rounded-full">
                <x-svg.img class="w-12 h-12 text-gray-600" />
            </div>
        @endif

        {{-- Informations sur le fichier --}}
        @if(isset($fileInfo['name']))
            <div class="w-full max-w-md bg-gray-50 p-4 rounded-lg flex-center flex-col gap-2">
                <h2 class="text-md-medium text-gray-800 truncate">{{ $fileInfo['name'] }}</h2>
                <p class="flex-center space-x-1.5 text-gray-600">
                    <span class="text-sm-regular">{{ strtoupper($fileInfo['extension'] ?? '') }}</span>
                    <span class="text-sm-regular">{{ $fileInfo['sizeFormatted'] ?? '' }}</span>
                </p>

                @if(isset($fileInfo['status']))
                    <p class="mt-2 px-3 py-1 text-xs rounded-full {{ $fileInfo['status'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} w-fit">
                        {{ $fileInfo['statusMessage'] ?? ($fileInfo['status'] === 'success' ? 'Fichier valide' : 'Erreur lors de l\'import du fichier') }}
                    </p>
                @endif
            </div>
        @endif
    </div>
</div>
