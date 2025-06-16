<div class="md:mx-2 relative flex flex-col gap-4 px-6 pt-4 pb-2 max-w-md bg-white dark:bg-gray-800 rounded-xl border border-slate-200">
        <div class="flex gap-3 h-full">
            <div class="flex flex-col gap-2">
                <h3 class="text-md-medium text-gray-900 dark:text-white">
                    👋🏻&nbsp;&nbsp;Cher, {{ auth()->user()->name ?? 'membre' }} !
                </h3>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    Vous m'avez personnellement demandé d'avoir l'option de personnaliser votre interface. Voici quelques options à explorer pour améliorer votre expérience utilisateur. La police 'Comic Sans' est particulièrement amusante, mais je vous laisse le choix de la police qui vous convient le mieux.
                </p>
            </div>
        </div>

        <!-- Bouton pour ouvrir la modale -->
        <button type="button" wire:click="togglePanel" class="button-primary w-fit">
            <span class="flex items-center gap-2 text-sm-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z" />
                </svg>
                Personnaliser l'interface
            </span>
        </button>

        <!-- Modale de personnalisation -->
        <x-modal wire:model="isOpen">
            <x-modal.panel width="400px">
                <form wire:submit.prevent="updateStyles">
                    <div class="flex flex-col gap-4 py-4">
                        <p class="sticky top-0 px-5 pr-16 max-w-full text-xl-bold bg-white dark:bg-gray-800 dark:border-gray-700 z-20">
                            Personnalisez votre interface selon vos préférences&nbsp;&nbsp;😈
                        </p>

                        <x-divider />

                        {{-- Contenu --}}
                        <div class="px-5 py-2">
                            <x-form.select
                                label="Police de caractères"
                                name="fontFamily"
                                model="fontFamily"
                            >
                                <option value="" disabled>Sélectionner une nouvelle police</option>
                                <option value="sans">Sans</option>
                                <option value="serif">Serif</option>
                                <option value="comic">Comic Sans</option>
                                <option value="apple">Apple</option>
                                <option value="arial">Arial</option>
                                <option value="verdana">Verdana</option>
                                <option value="courier">Courier New</option>
                                <option value="monospace">Monospace</option>
                                <option value="garamond">Garamond</option>
                                <option value="tahoma">Tahoma</option>
                                <option value="trebuchet">Trebuchet MS</option>
                                <option value="lucida">Lucida Sans</option>
                            </x-form.select>

                            <p class="mt-2 mx-2 text-xs text-gray-500 dark:text-gray-500 mb-2">
                                {{ __('Sélectionnez la police qui vous convient le mieux') }}
                            </p>

                            <p class="mt-6 mb-2.5 pl-2 block text-sm-medium text-gray-800 dark:text-gray-200">
                                {{ __('Zoom de la page') }}
                            </p>
                            <x-radio-group
                                label="Zoom de la page"
                                wire:model.live="zoomLevel"
                                class="flex flew-wrap gap-2.5 justify-between mx-1"
                            >
                                <x-radio-group.option value="90" class="flex flex-col items-center bg-gray-50 dark:bg-gray-700 rounded-md p-2.5 border border-gray-200 dark:border-gray-600 cursor-pointer flex-1" class-checked="ring-2 ring-blue-500 bg-white dark:bg-gray-750" class-not-checked="hover:bg-gray-100 dark:hover:bg-gray-650" x-transition:enter="transition ease-out">
                                    <span class="text-xs-medium text-gray-700 dark:text-gray-300">90%</span>
                                </x-radio-group.option>
                                <x-radio-group.option value="100" class="flex flex-col items-center bg-gray-50 dark:bg-gray-700 rounded-md p-2.5 border border-gray-200 dark:border-gray-600 cursor-pointer flex-1" class-checked="ring-2 ring-blue-500 bg-white dark:bg-gray-750" class-not-checked="hover:bg-gray-100 dark:hover:bg-gray-650" x-transition:enter="transition ease-out">
                                    <span class="text-xs-medium text-gray-700 dark:text-gray-300">100%</span>
                                </x-radio-group.option>
                                <x-radio-group.option value="110" class="flex flex-col items-center bg-gray-50 dark:bg-gray-700 rounded-md p-2.5 border border-gray-200 dark:border-gray-600 cursor-pointer flex-1" class-checked="ring-2 ring-blue-500 bg-white dark:bg-gray-750" class-not-checked="hover:bg-gray-100 dark:hover:bg-gray-650" x-transition:enter="transition ease-out">
                                    <span class="text-xs-medium text-gray-700 dark:text-gray-300">110%</span>
                                </x-radio-group.option>
                                <x-radio-group.option value="125" class="flex flex-col items-center bg-gray-50 dark:bg-gray-700 rounded-md p-2.5 border border-gray-200 dark:border-gray-600 cursor-pointer flex-1" class-checked="ring-2 ring-blue-500 bg-white dark:bg-gray-750" class-not-checked="hover:bg-gray-100 dark:hover:bg-gray-650" x-transition:enter="transition ease-out">
                                    <span class="text-xs-medium text-gray-700 dark:text-gray-300">125%</span>
                                </x-radio-group.option>
                            </x-radio-group>

                            <p class="mt-2 mx-2 text-xs text-gray-500 dark:text-gray-500 mb-2">
                                {{ __('Ajustez la taille du contenu selon vos préférences') }}
                            </p>
                        </div>
                    </div>
                    <x-modal.footer>
                        <button type="button" wire:click="resetStyles" class="button-classic">
                            <x-svg.reset />
                            Réinitialiser
                        </button>
                        <button type="submit" class="button-primary">
                            <x-svg.validate />
                            Appliquer
                        </button>
                    </x-modal.footer>
                </form>
            </x-modal.panel>
        </x-modal>
    </div>
