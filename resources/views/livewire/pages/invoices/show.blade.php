<div class="grid grid-cols-[40vw_auto] mx-auto lg:gap-8">
    <div class="relative flex-center overflow-hidden border border-slate-200 bg-gray-100 rounded-xl">
        <x-file-viewer
            :filePath="$filePath"
            :fileExtension="$fileExtension"
            :fileName="$fileName"
            class="w-full h-full min-h-[50vh]"
        />
    </div>

    <x-invoices.create.summary :form="$invoice" />
</div>
