<div class="flex flex-col gap-6">

    <h2 role="heading" aria-level="2" class="p-4 pb-0 text-2xl font-bold text-gray-900">
        Détails de la facture
    </h2>

    <div class="relative flex-center w-full h-full overflow-hidden border border-slate-200 bg-gray-100 rounded-xl">
        <x-file-viewer
            :$filePath
            :$fileExtension
            :$fileName
            class="w-full h-full min-h-[50vh]"
        />
    </div>

    <x-invoices.create.summary :$form :$family_members />
</div>
