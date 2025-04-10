<div class="flex flex-col justify-center lg:grid lg:grid-cols-[40vw_auto] gap-6">
    <div class="relative flex-center w-full h-full overflow-hidden border border-slate-200 bg-gray-100 rounded-xl">
        <x-file-viewer
            :$filePath
            :$fileExtension
            :$fileName
            class="w-full h-full min-h-[50vh]"
        />
    </div>

    <h2 role="heading" aria-level="2" class="sr-only">Détails de la facture</h2>
    <x-invoices.create.summary :form="$invoice" :family_members="$family_members" />
</div>
