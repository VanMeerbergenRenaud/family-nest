<div class="flex flex-col gap-6 p-4">

    <h2 role="heading" aria-level="2" class="px-4 text-2xl font-bold text-gray-900">
        Détails de la facture
    </h2>

    <div class="relative flex-center w-full h-full max-h-[75vh] overflow-hidden border border-slate-200 bg-gray-100 rounded-xl">
        <x-invoices.file-viewer
            :$filePath
            :$fileExtension
            :$fileName
            class="w-full h-full min-h-[50vh]"
        />
    </div>

    <x-invoices.form.summary :$form :$family_members />
</div>
