<div class="flex-center flex-col lg:grid lg:grid-cols-[40vw_auto] gap-6">
    <div class="relative flex-center w-full h-full overflow-hidden border border-slate-200 bg-gray-100 rounded-xl">
        <x-file-viewer
            :$filePath
            :$fileExtension
            :$fileName
            class="w-full h-full min-h-[50vh]"
        />
    </div>

    <x-invoices.create.summary :form="$invoice" :family_members="$family_members" />
</div>
