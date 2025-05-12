@props([
                                     'label',
                                     'name',
                                     'model',
                                     'asterix' => false,
                                     'title' => 'Traitement en cours',
                                     'description' => null,
                                     'maxSizeMB' => 10,
                                 ])

<x-form.field-base :label="$label" :name="$name" :model="$model" :asterix="$asterix" class="w-full">
    <div x-data="{
                                             uploading: false,
                                             progress: 0,
                                             fileError: null,
                                             maxSizeBytes: {{ $maxSizeMB }} * 1024 * 1024,

                                             validateFile(event) {
                                                 const file = event.target.files[0];
                                                 if (!file) return true;

                                                 if (file.size > this.maxSizeBytes) {
                                                     this.fileError = `Le fichier est trop volumineux (${(file.size / (1024 * 1024)).toFixed(2)} Mo). Maximum {{ $maxSizeMB }} Mo.`;
                                                     this.resetFile(event.target);
                                                     return false;
                                                 }

                                                 const fileExt = '.' + file.name.split('.').pop().toLowerCase();
                                                 if (!['.pdf', '.docx', '.jpeg', '.jpg', '.png'].includes(fileExt)) {
                                                     this.fileError = 'Format de fichier non accepté. Utilisez PDF, DOCX, JPG ou PNG.';
                                                     this.resetFile(event.target);
                                                     return false;
                                                 }

                                                 this.fileError = null;
                                                 return true;
                                             },

                                             resetFile(input) {
                                                 input.value = '';
                                                 $wire.set('{{ $model }}', null);
                                             }
                                         }"
         x-on:livewire-upload-start="uploading = true"
         x-on:livewire-upload-finish="uploading = false"
         x-on:livewire-upload-cancel="uploading = false"
         x-on:livewire-upload-error="uploading = false"
         x-on:livewire-upload-progress="progress = $event.detail.progress"
         class="@error($name) input-invalid @enderror relative border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-slate-50 hover:bg-slate-100 dark:bg-gray-700 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 mx-1"
    >
        <div class="flex-center flex-col p-10 min-h-[25rem]">
            <x-svg.download class="w-5 h-5 mb-5"/>

            <p class="mb-4">
                                                 <span
                                                     class="text-md-medium text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-500">
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

            <div x-show="fileError" x-cloak class="mt-4 px-3 py-2 bg-red-100 text-red-800 text-xs rounded-md"
                 x-text="fileError"></div>
        </div>

        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="file"
            wire:model.blur="{{ $model }}"
            @change="validateFile($event)"
            accept=".pdf,.docx,.jpeg,.jpg,.png"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
            {{ $attributes }}
        >

        <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 max-w-md w-80 rounded-xl border border-transparent">
            <div x-cloak wire:loading wire:target="{{ $model }}" class="w-full">
                <div class="relative z-20 bg-white rounded-xl border border-slate-200">
                    <div class="flex items-center px-5 py-3 gap-5">
                        <div class="flex-shrink-0">
                            <x-svg.spinner class="text-blue-500 size-5"/>
                        </div>

                        <div class="flex-grow">
                            <div class="font-medium text-gray-800">{{ $title }}</div>
                            @if($description)
                                <div class="text-sm text-gray-500 mt-1 leading-snug">{{ $description }}</div>
                            @endif
                        </div>
                    </div>

                    <div x-show="uploading" class="w-full px-4 mb-2">
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div x-bind:style="'width: ' + progress + '%'"
                                 class="bg-blue-500 h-full rounded-full transition-all duration-300"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1 text-right" x-text="progress + '%'"></div>
                    </div>

                    <button
                        type="button"
                        @click="$wire.set('{{ $model }}', null)"
                        class="absolute top-0 right-0.5 p-3 z-10 cursor-pointer"
                        aria-label="Fermer le dialogue de chargement"
                    >
                        <x-svg.cross/>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-form.field-base>
