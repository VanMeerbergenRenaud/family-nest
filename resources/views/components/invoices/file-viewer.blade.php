@props([
    'filePath',
    'fileExtension',
    'fileName',
])

<div {{ $attributes->merge(['class' => 'w-full h-full relative flex-center bg-gray-100 rounded-xl overflow-y-scroll overflow-x-hidden']) }}">
    @if ($filePath)
        @if(in_array($fileExtension, ['jpg', 'jpeg', 'png']))
            <img
                src="{{ $filePath }}"
                alt="{{ $fileName ?? 'Aperçu du document' }}"
                class="max-w-full max-h-full object-cover"
            >
        @elseif($fileExtension === 'pdf')
            <object
                id="pdf-viewer"
                width="100%"
                height="100%"
                class="inline-block w-full h-full min-h-[60vh]"
                type="application/pdf"
                data="{{ $filePath }}"
                sandbox="allow-same-origin"
            ></object>
        @elseif($fileExtension === 'docx')
            <div class="p-5 text-gray-700 text-md-medium">
                <div class="w-16 h-16 mx-auto mb-6 flex-center bg-blue-100 rounded-full">
                    <x-svg.docx class="w-8 h-8" />
                </div>
                <p class="text-center mb-6 font-medium">Aperçu non disponible pour les fichiers WORD</p>
                <p class="text-center">
                    <a href="{{ $filePath }}" target="_blank" class="text-sm-medium text-indigo-600 hover:underline">Télécharger le fichier</a>
                </p>
            </div>
        @elseif($fileExtension === 'csv')
            <div class="p-5 text-gray-700 text-md-medium">
                <div class="w-16 h-16 mx-auto mb-6 flex-center bg-green-100 rounded-full">
                    <x-svg.csv class="w-8 h-8" />
                </div>
                <p class="text-center mb-6 font-medium">Aperçu non disponible pour les fichiers CSV</p>
                <p class="text-center">
                    <a href="{{ $filePath }}" target="_blank" class="text-sm-medium text-green-600 hover:underline">Télécharger le fichier</a>
                </p>
            </div>
        @else
            <div class="flex-center flex-col p-8">
                <x-svg.file-size class="w-24 h-24 text-gray-400 mb-6"/>
                <p class="text-xl-medium text-gray-700 dark:text-gray-300 mb-2">
                    Prévisualisation non disponible
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center max-w-md mb-8">
                    Le type de fichier "{{ strtoupper($fileExtension) }}" ne peut pas être prévisualisé
                    directement. Veuillez télécharger le fichier pour le consulter.
                </p>
                <a href="{{ $filePath }}" class="button-primary">
                    <x-svg.download class="mr-2"/>
                    Télécharger le fichier
                </a>
            </div>
        @endif
    @else
        <div class="p-5 text-gray-700 text-md-medium">
            <div class="w-16 h-16 mx-auto mb-4 flex-center bg-red-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <p class="text-center mb-2.5 font-medium">Aucun fichier disponible</p>
            <p class="text-center text-sm text-gray-500">
                Le fichier n'a pas été trouvé ou n'est plus disponible sur le serveur.
            </p>
        </div>
    @endif
</div>
