<div class="grid grid-cols-[40vw_auto] mx-auto lg:gap-8">
    <div class="relative flex-center overflow-hidden border border-slate-200 bg-gray-100 rounded-xl">

        @php
            $uploadedFile = $invoice->file->file_path ?? null;

            $isImage = $uploadedFile && preg_match('/\.(jpg|jpeg|png|gif)$/i', $uploadedFile);
            $isPdf = $uploadedFile && preg_match('/\.pdf$/i', $uploadedFile);
            $isDocx = $uploadedFile && preg_match('/\.docx?$/i', $uploadedFile);
            $isCsv = $uploadedFile && preg_match('/\.csv$/i', $uploadedFile);
        @endphp

        @if ($isImage)
            <img src="{{ $uploadedFile }}" alt="Aperçu de la facture" />
        @elseif ($isPdf)
            <embed src="{{ $uploadedFile }}" type="application/pdf" width="100%" height="100%" />
        @elseif ($isDocx)
            <p class="px-4 text-gray-700 text-center">Aperçu non disponible pour les fichiers Word.</p>
        @elseif($isCsv)
            <p class="px-4 text-gray-700 text-center">Aperçu non disponible pour les fichiers CSV.</p>
        @else
            <p class="px-4 text-gray-700 text-center">Aperçu non disponible pour ce type de fichier enregistré.</p>
        @endif
    </div>

    <x-invoices.create.summary :form="$invoice" :family_members="$family_members"/>
</div>
