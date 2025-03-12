<div class="mb-2 lg:mb-6">
    <!-- Navigation mobile -->
    <div class="lg:hidden flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-800 md:max-w-[60vw] mx-auto">

        <button
            @click="currentStep > 1 ? prevStep() : null"
            :class="{'opacity-50 cursor-not-allowed': currentStep <= 1, 'cursor-pointer': currentStep > 1}"
            class="p-2"
        >
            <x-svg.arrows.left class="w-5 h-5 text-gray-600 dark:text-gray-300" />
        </button>

        <!-- Menu déroulant des étapes -->
        <x-menu>
            <x-menu.button class="button-primary min-w-56 justify-center">
                <span class="font-medium" x-text="`Étape ${currentStep} : ${steps[currentStep-1]}`"></span>
                <x-svg.arrows.right class="ml-1 rotate-90" />
            </x-menu.button>

            <x-menu.items class="mt-2 w-56">
                <template x-for="(step, index) in steps" :key="index">
                    <x-menu.item @click="goToStep(index + 1)">
                        <span class="relative w-full flex items-center">
                            <span class="w-6 h-6 rounded-full flex-center mr-2"
                                  :class="{ 'bg-slate-700 text-white': currentStep === index + 1, 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200': currentStep !== index + 1 }">
                                <span x-text="index + 1" class="text-xs"></span>
                            </span>
                            <span class="text-sm text-gray-700 dark:text-gray-200" x-text="step"></span>
                            <!-- Icône de vérification pour l'étape active -->
                            <svg x-show="currentStep === index + 1" class="h-4 w-4 absolute right-0"
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </x-menu.item>
                </template>
            </x-menu.items>
        </x-menu>

        <button
            @click="currentStep < steps.length ? nextStep() : null"
            :class="{'opacity-50 cursor-not-allowed': currentStep >= steps.length, 'cursor-pointer': currentStep < steps.length}"
            class="p-2"
        >
            <x-svg.arrows.right class="w-5 h-5 text-gray-600 dark:text-gray-300" />
        </button>
    </div>

    <!-- Navigation desktop -->
    <div class="hidden lg:flex-center lg:flex-row lg:flex-wrap lg:px-6 lg:py-4 lg:rounded-xl lg:space-x-2">
        <template x-for="(step, index) in steps" :key="index">
            <div class="flex-center cursor-pointer whitespace-nowrap mb-4" @click="goToStep(index + 1)">

                <span class="w-8 h-8 rounded-full flex-center mr-3"
                      :class="{ 'bg-slate-700 text-white': currentStep === index + 1, 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200': currentStep !== index + 1 }">
                    <span x-text="index + 1" class="text-sm"></span>
                </span>

                <span class="text-md-regular text-slate-700 dark:text-slate-200"
                      :class="{ 'font-medium underline': currentStep === index + 1 }"
                      x-text="step">
                </span>

                <span x-show="index < steps.length - 1" class="mx-2 text-slate-400 dark:text-slate-500">
                    <x-svg.chevron-right class="w-4 h-4 md:w-6 md:h-6"/>
                </span>
            </div>
        </template>
    </div>
</div>
