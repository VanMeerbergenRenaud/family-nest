@props([
     'label',
     'name',
     'model',
     'asterix' => false,
     'title' => 'Traitement en cours',
     'maxSizeMB' => 10,
 ])

<x-form.field-base :label="$label" :name="$name" :model="$model" :asterix="$asterix" class="w-full">
    <div
        x-data="fileUploader('{{ $model }}', {{ $maxSizeMB }})"
        x-on:livewire-upload-start="uploading = true"
        x-on:livewire-upload-finish="uploading = false"
        x-on:livewire-upload-cancel="uploading = false"
        x-on:livewire-upload-error="uploading = false; fileError = 'Une erreur s\'est produite lors du téléchargement.'"
        x-on:livewire-upload-progress="progress = $event.detail.progress"
        class="@error($name) input-invalid @enderror relative border-2 border-dashed rounded-lg cursor-pointer bg-slate-50 hover:bg-slate-100 dark:bg-gray-700 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 mx-1 border-gray-300"
    >
        <div class="flex-center flex-col p-10 min-h-[25rem]">
            <x-svg.download class="w-5 h-5 mb-5"/>

            <p class="mb-4">
                 <span class="text-md-medium text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-500">
                     Clic pour importer
                 </span>
                <span class="block text-sm text-center text-gray-500 dark:text-gray-400">
                     ou glisser déposer
                 </span>
            </p>

            <p class="text-xs text-center max-w-48 text-gray-500 dark:text-gray-400">
                PDF, DOCX, PNG, JPG ou JPEG (MAX. {{ $maxSizeMB }} Mo)
            </p>

            <div class="mt-3 flex flex-wrap justify-center gap-2">
                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">PDF</span>
                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">DOCX</span>
                <span class="px-2 py-1 bg-green-200 text-green-800 text-xs rounded-full">IMG</span>
            </div>

            <div x-cloak
                 x-show="fileError"
                 x-text="fileError"
                 class="mt-6 px-3 py-2 text-xs text-center rounded-md bg-red-100 text-red-800"
            ></div>
        </div>

        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="file"
            x-ref="fileInput"
            @change="handleFile($event)"
            accept=".pdf,.docx,.jpeg,.jpg,.png"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
            {{ $attributes }}
        >

        <!-- Toast de progression -->
        <div x-cloak x-show="uploading" class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 max-w-md w-80 rounded-xl">
            <div class="bg-white rounded-xl border border-slate-200 w-full">
                <div class="flex px-5 py-3 gap-5">
                    <x-svg.spinner class="text-blue-500 size-5 mt-1.5 flex-shrink-0"/>
                    <div class="flex-grow">
                        <div class="font-medium text-gray-800">{{ $title }}</div>
                        <div x-show="fileName" class="mt-1">
                            <p class="text-sm text-gray-500 leading-snug">
                                Nom du fichier : <span class="text-sm text-gray-500 leading-snug" x-text="formatFileName(fileName)"></span>
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click.stop="cancelUpload()"
                        class="absolute top-1 right-1.5 p-2 cursor-pointer"
                        aria-label="Annuler le téléchargement"
                    >
                        <x-svg.cross class="w-4.5 h-4.5" />
                    </button>
                </div>

                <div class="w-full px-4 mb-2">
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div x-bind:style="'width: ' + progress + '%'" class="bg-blue-500 h-full rounded-full transition-all duration-300"></div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1 text-right" x-text="progress + '%'"></div>
                </div>
            </div>
        </div>
    </div>
</x-form.field-base>

@script
<script>
    Alpine.data('fileUploader', (model, maxSizeMB) => ({
        uploading: false,
        progress: 0,
        fileError: null,
        fileName: null,

        handleFile(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.fileError = null;
            this.fileName = file.name;

            // Validation de la taille
            if (file.size > maxSizeMB * 1024 * 1024) {
                this.fileError = `Fichier trop volumineux (${(file.size / (1024 * 1024)).toFixed(2)} Mo). Maximum ${maxSizeMB} Mo.`;
                this.$refs.fileInput.value = '';
                return;
            }

            // Validation de l'extension
            const ext = '.' + file.name.split('.').pop().toLowerCase();
            if (!['.pdf', '.docx', '.jpeg', '.jpg', '.png'].includes(ext)) {
                this.fileError = 'Format non accepté. Utilisez PDF, DOCX, JPG ou PNG.';
                this.$refs.fileInput.value = '';
                return;
            }

            // Upload via Livewire
            this.uploading = true;

            this.$wire.upload(model, file,
                () => { this.uploading = false; }, // Succès
                () => { // Erreur
                    this.uploading = false;
                    this.fileError = "Une erreur s'est produite lors du téléchargement.";
                },
                (event) => { this.progress = event.detail.progress; } // Progression
            );
        },

        formatFileName(name) {
            return name?.length > 50
                ? name.substring(0, 47) + '...'
                : name;
        },

        cancelUpload() {
            this.$refs.fileInput.value = '';
            this.fileName = null;
            this.fileError = null;
            this.progress = 0;
            this.uploading = false;
            this.$wire.set(model, null);
        }
    }));
</script>
@endscript
