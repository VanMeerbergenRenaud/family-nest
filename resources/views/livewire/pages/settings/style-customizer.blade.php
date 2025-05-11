<div class="mx-2 relative flex flex-col items-end gap-4 px-5.5 py-4 max-w-4xl bg-white dark:bg-gray-800 rounded-xl border border-slate-200">
    <div class="flex gap-3 h-full">
        <div class="flex-shrink-0">
            👋🏻
        </div>
        <div>
            <h3 class="text-md-medium text-gray-900 dark:text-white mb-1">
                Cher, {{ auth()->user()->name ?? 'membre' }} !
            </h3>
            <p class="text-md text-gray-600 dark:text-gray-300 leading-relaxed">
                Vous m'avez personnellement demandé d'avoir l'option de personnaliser votre interface. Voici quelques options à explorer pour améliorer votre expérience utilisateur. La police 'Comic Sans' est particulièrement amusante, mais je vous laisse le choix de la police qui vous convient le mieux.
            </p>
        </div>
    </div>
    <x-menu>
        <!-- Bouton principal amélioré -->
        <x-menu.button
            type="button"
            wire:click="togglePanel"
            class="button-primary"
            title="Personnaliser l'interface"
        >
        <span class="flex items-center gap-2 text-sm-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z" />
            </svg>
            Changer la feuille de style
        </span>
        </x-menu.button>

        {{-- Dropdown list --}}
        <x-menu.items>
            <div class="bg-white dark:bg-gray-800 rounded-lg py-1.5 w-full">
                <h3 class="text-sm-medium text-gray-800 dark:text-white mb-3 pl-2 pr-4">
                    Personnaliser votre interface cher {{ auth()->user()->name ?? 'membre' }}&nbsp;!
                </h3>

                <x-menu.divider />

                <div class="mt-4 flex flex-col gap-4 px-1">
                    <!-- Police avec description -->
                    <div>
                        <x-form.select
                            label="Police de caractères"
                            name="fontFamily"
                            model="fontFamily"
                        >
                            <option value="" disabled>Sélectionner une police</option>
                            <option value="sans">Sans</option>
                            <option value="serif">Serif</option>
                            <option value="comic">Comic Sans</option>
                            <option value="apple">Apple</option>
                        </x-form.select>
                        <p class="mt-2 mx-2 text-xs text-gray-500 dark:text-gray-500 mb-2">Sélectionnez la police qui vous convient le mieux</p>
                    </div>

                    <!-- Zoom avec boutons radio améliorés -->
                    <div>
                        <h3 class="relative mb-2.5 pl-2 block text-sm-medium text-gray-800 dark:text-gray-200">
                            Zoom de la page
                        </h3>
                        <x-radio-group
                            label="Zoom de la page"
                            wire:model.live="zoomLevel"
                            class="flex flew-wrap gap-2.5 justify-between mx-1"
                        >
                            <x-radio-group.option
                                value="90"
                                class="flex flex-col items-center bg-gray-50 dark:bg-gray-700 rounded-md p-2.5 border border-gray-200 dark:border-gray-600 cursor-pointer flex-1"
                                class-checked="ring-2 ring-blue-500 bg-white dark:bg-gray-750"
                                class-not-checked="hover:bg-gray-100 dark:hover:bg-gray-650"
                                x-transition:enter="transition ease-out"
                            >
                                <span class="text-xs-medium text-gray-700 dark:text-gray-300">90%</span>
                            </x-radio-group.option>

                            <x-radio-group.option
                                value="100"
                                class="flex flex-col items-center bg-gray-50 dark:bg-gray-700 rounded-md p-2.5 border border-gray-200 dark:border-gray-600 cursor-pointer flex-1"
                                class-checked="ring-2 ring-blue-500 bg-white dark:bg-gray-750"
                                class-not-checked="hover:bg-gray-100 dark:hover:bg-gray-650"
                                x-transition:enter="transition ease-out"
                            >
                                <span class="text-xs-medium text-gray-700 dark:text-gray-300">100%</span>
                            </x-radio-group.option>

                            <x-radio-group.option
                                value="110"
                                class="flex flex-col items-center bg-gray-50 dark:bg-gray-700 rounded-md p-2.5 border border-gray-200 dark:border-gray-600 cursor-pointer flex-1"
                                class-checked="ring-2 ring-blue-500 bg-white dark:bg-gray-750"
                                class-not-checked="hover:bg-gray-100 dark:hover:bg-gray-650"
                                x-transition:enter="transition ease-out"
                            >
                                <span class="text-xs-medium text-gray-700 dark:text-gray-300">110%</span>
                            </x-radio-group.option>

                            <x-radio-group.option
                                value="125"
                                class="flex flex-col items-center bg-gray-50 dark:bg-gray-700 rounded-md p-2.5 border border-gray-200 dark:border-gray-600 cursor-pointer flex-1"
                                class-checked="ring-2 ring-blue-500 bg-white dark:bg-gray-750"
                                class-not-checked="hover:bg-gray-100 dark:hover:bg-gray-650"
                                x-transition:enter="transition ease-out"
                            >
                                <span class="text-xs-medium text-gray-700 dark:text-gray-300">125%</span>
                            </x-radio-group.option>
                        </x-radio-group>
                        <p class="mt-2 mx-2 text-xs text-gray-500 dark:text-gray-500 mb-2">Ajustez la taille du contenu selon vos préférences</p>
                    </div>

                    <!-- Boutons d'actions -->
                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" wire:click="resetStyles" class="button-classic">
                            <x-svg.reset />
                            Réinitialiser
                        </button>

                        <button type="button" wire:click="updateStyles" class="button-primary">
                            <x-svg.validate />
                            Appliquer
                        </button>
                    </div>
                </div>
            </div>
        </x-menu.items>
    </x-menu>
</div>
