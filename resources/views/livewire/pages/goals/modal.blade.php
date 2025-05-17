<div>
    <x-modal wire:model="showModal">
        <x-modal.panel position="top-right" height="96vh">
            <form wire:submit.prevent="saveGoal">
                @csrf

                <h2 role="heading" aria-level="2" class="sticky top-0 p-5 px-8 max-w-full text-xl-bold bg-white border-b border-slate-200 dark:bg-gray-800 dark:border-gray-700 z-20">
                    {{ $isEditMode ? __('Modifier l\'objectif') : __('Créer un nouvel objectif') }}
                </h2>

                <div class="p-4 md:p-6 flex flex-col md:grid md:grid-cols-2 gap-4">

                    <x-form.field
                        label="Nom de l'objectif"
                        name="form.name"
                        model="form.name"
                        placeholder="Exemple: Budget courses du mois"
                        :asterix="true"
                    />

                    <x-form.select name="form.period_type" model="form.period_type" label="Période" :asterix="true">
                        @foreach($periodTypes as $period)
                            <option value="{{ $period->value }}">{{ $period->label() }}</option>
                        @endforeach
                    </x-form.select>

                    <div class="col-span-2">
                        <x-form.field-textarea
                            label="Description"
                            name="form.description"
                            model="form.description"
                            placeholder="Décrivez votre objectif..."
                        />
                    </div>

                    <x-form.field-currency
                        label="Montant cible"
                        name="form.target_amount"
                        model="form.target_amount"
                        defaultCurrency="EUR"
                        placeholder="0,00"
                        :initialValue="$form->target_amount"
                        :asterix="true"
                    />

                    <x-form.select name="form.goal_type" model="form.goal_type" label="Type d'objectif" :asterix="true">
                        @foreach($goalTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </x-form.select>

                    <x-form.field-date
                        label="Date de début"
                        name="form.start_date"
                        model="form.start_date"
                        :asterix="true"
                    />

                    <x-form.field-date
                        label="Date de fin"
                        name="form.end_date"
                        model="form.end_date"
                        :asterix="true"
                    />

                    <div class="col-span-2">
                        <h3 role="heading" aria-level="3" class="relative p-2 pt-0 text-sm-medium text-gray-800">
                            Catégories
                            <span aria-hidden="true" class="absolute -top-0.5 ml-0.5 text-rose-500">*</span>
                        </h3>

                        <!-- Filtre de recherche amélioré -->
                        <div class="mb-2 flex flex-wrap items-center gap-2">
                            <div class="relative">
                                <x-svg.search-classic class="absolute top-3 left-3.5 text-gray-400" />
                                <input
                                    type="text"
                                    placeholder="Rechercher une catégorie..."
                                    wire:model.live.debounce.200ms="categorySearch"
                                    class="w-full min-w-[22.5rem] pl-10 pr-3 py-2 placeholder:text-[0.9375rem] text-[0.9375rem] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200"
                                >

                                @if($categorySearch)
                                    <button
                                        type="button"
                                        wire:click="$set('categorySearch', '')"
                                        class="absolute top-3 right-3 flex items-center text-gray-400 hover:text-gray-600"
                                    >
                                        <x-svg.cross />
                                    </button>
                                @endif
                            </div>

                            {{-- Filtres et affichage --}}
                            <div class="flex justify-end gap-2">

                                <!-- Filtres -->
                                <x-menu>
                                    <x-menu.button class="button-primary justify-between">
                                        <x-svg.filter />
                                        Filtres
                                    </x-menu.button>

                                    <x-menu.items class="w-max max-h-[20rem] overflow-y-auto">
                                        @foreach(\App\Enums\TypeEnum::cases() as $type)
                                            <x-menu.item
                                                wire:click="filterByType('{{ $type->value }}')"
                                            >
                                                {{ $type->emoji() }}&nbsp;&nbsp;{{ $type->label() }}
                                            </x-menu.item>
                                        @endforeach
                                    </x-menu.items>
                                </x-menu>

                                {{-- Toggle liste/grille  --}}
                                <button
                                    type="button"
                                    wire:click="toggleCategoryView"
                                    class="button-primary flex items-center gap-2"
                                >
                                    @if($categoryView === 'list')
                                        <x-svg.queue-list />
                                        Mode liste
                                    @else
                                        <x-svg.squares-2x2 />
                                        Mode grille
                                    @endif
                                </button>
                            </div>
                        </div>

                        <!-- Message d'erreur -->
                        @error('form.categories')
                            <p class="mb-3 pl-2 text-sm-medium text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Liste des catégories -->
                        <div class="col-span-2 grid grid-cols-1 max-h-[20rem] overflow-y-auto rounded-lg border border-gray-200 bg-white">
                            @if($categoryView === 'list')
                                @forelse($this->categoriesByType as $typeData)
                                    <div class="p-3 bg-white">
                                        <div class="flex items-center justify-between mb-2 pb-1.5 px-2 border-b border-gray-200 dark:border-gray-700">
                                            <h4 role="heading" aria-level="4" class="flex items-center gap-2">
                                                <span class="text-sm-bold text-gray-700 dark:text-gray-300">{{ $typeData['type']->emoji() }}</span>
                                                <span class="text-sm-bold text-gray-700 dark:text-gray-300">{!! $typeData['type']->label() !!}</span>
                                            </h4>
                                        </div>

                                        <ul class="flex flex-col gap-2 px-2">
                                            @foreach($typeData['categories'] as $category)
                                                <li class="{{ in_array($category->value, $form->categories) ? 'bg-indigo-50 rounded-md' : '' }}">
                                                    <x-form.checkbox-input
                                                        label="{{ $category->emoji() }} {!! $category->label() !!}"
                                                        name="form.categories.{{ $category->value }}"
                                                        wire:model.live="form.categories"
                                                        value="{{ $category->value }}"
                                                    />
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @empty
                                    <p class="px-4 py-3 text-sm text-gray-500">
                                        {{ __('Aucune catégorie ne correspond à votre recherche.') }}
                                    </p>
                                @endforelse
                            @else
                                <div class="p-3 grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @php
                                        $allCategories = [];
                                        foreach ($this->categoriesByType as $typeData) {
                                            foreach ($typeData['categories'] as $category) {
                                                $allCategories[] = $category;
                                            }
                                        }
                                    @endphp

                                    @forelse($allCategories as $category)
                                        <div wire:click="toggleCategory('{{ $category->value }}')"
                                             class="p-2 border rounded-md cursor-pointer hover:bg-gray-50 transition-colors flex items-center gap-2 {{ in_array($category->value, $form->categories) ? 'bg-indigo-50 border-indigo-300' : 'border-gray-200' }}"
                                        >
                                            <span>{{ $category->emoji() }}</span>
                                            <span class="text-xs truncate" title="{{ $category->label() }}">{!! $category->label() !!}</span>
                                        </div>
                                    @empty
                                        <p class="px-4 py-3 text-sm text-gray-500 col-span-3">
                                            {{ __('Aucune catégorie ne correspond à votre recherche.') }}
                                        </p>
                                    @endforelse
                                </div>
                            @endif
                        </div>

                        <!-- Affichage des catégories sélectionnées -->
                        <div class="mt-4">
                            <div class="pl-2 pr-2.5 flex items-center justify-between">
                                <p class="text-sm text-gray-600 mb-2">
                                    <span class="text-sm-medium">{{ count($form->categories) }}</span> catégorie(s) sélectionnée(s)
                                </p>
                                @if(count($form->categories) > 0)
                                    <button
                                        type="button"
                                        wire:click="$set('form.categories', [])"
                                        class="text-sm-medium text-red-600"
                                    >
                                        Tout désélectionner
                                    </button>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-2 mb-3 p-2 bg-gray-50 border border-gray-200 rounded-lg min-h-12 {{ count($form->categories) === 0 ? 'items-center justify-center' : '' }}">
                                @forelse($form->categories as $categoryValue)
                                    @php
                                        $foundCategory = null;
                                        foreach (\App\Enums\TypeEnum::cases() as $type) {
                                            foreach ($type->categoryEnums() as $category) {
                                                if ($category->value === $categoryValue) {
                                                    $foundCategory = $category;
                                                    break 2;
                                                }
                                            }
                                        }
                                    @endphp

                                    @if($foundCategory)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-indigo-50 text-indigo-700 rounded-md text-xs">
                                            {{ $foundCategory->emoji() }} {{ $foundCategory->label() }}
                                            <button type="button" wire:click="removeCategory('{{ $categoryValue }}')" class="text-indigo-500 hover:text-indigo-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </span>
                                    @endif
                                @empty
                                    <p class="text-sm text-gray-500">Aucune catégorie sélectionnée</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Option familial -->
                    <label
                        wire:click="$set('form.is_family_goal', true)"
                        class="group bg-gray-50 cursor-pointer rounded-lg border border-gray-50 px-4 py-3 flex gap-3 transition-all
                        {{ $form->is_family_goal ? 'bg-indigo-50 border-indigo-300' : 'hover:bg-gray-100' }}"
                    >
                        <x-svg.user-group class="min-w-4 mt-0.5 {{ $form->is_family_goal ? 'text-indigo-600' : 'text-gray-700' }}" />
                        <div>
                            <p class="text-sm-medium {{ $form->is_family_goal ? 'text-indigo-700' : 'text-gray-700' }}">Familial</p>
                            <p class="text-xs {{ $form->is_family_goal ? 'text-indigo-600' : 'text-gray-500' }} mt-0.5">Cet objectif sera partagé avec tous les membres de votre famille</p>
                        </div>
                    </label>

                    <!-- Option privé -->
                    <label
                        wire:click="$set('form.is_family_goal', false)"
                        class="group bg-gray-50 cursor-pointer rounded-lg border border-gray-50 px-4 py-3 flex gap-3 transition-all
                        {{ !$form->is_family_goal ? 'bg-indigo-50 border-indigo-300' : 'hover:bg-gray-100' }}"
                    >
                        <x-svg.lock-closed class="min-w-4 mt-0.5 {{ !$form->is_family_goal ? 'text-indigo-600' : 'text-gray-700' }}" />
                        <div>
                            <p class="text-sm-medium {{ !$form->is_family_goal ? 'text-indigo-700' : 'text-gray-700' }}">Privé</p>
                            <p class="text-xs {{ !$form->is_family_goal ? 'text-indigo-600' : 'text-gray-500' }} mt-0.5">Cet objectif ne sera visible que par vous</p>
                        </div>
                    </label>

                    {{-- Affichage des erreurs juste ici --}}
                    @if($errors->any())
                        <div class="mb-4 col-span-2">
                            <div class="p-2 bg-red-100 border border-red-300 text-red-600 rounded-lg">
                                <p class="p-2 pt-0 text-sm-medium text-red-600">Liste des erreurs :</p>
                                <ul class="pl-5 pb-2 space-y-2 list-disc">
                                    @foreach($errors->all() as $error)
                                        <li class="text-sm-regular text-red-600">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>

                <x-modal.footer>
                    <x-modal.close>
                        <button type="button" class="button-primary">
                            {{ __('Annuler') }}
                        </button>
                    </x-modal.close>

                    <button type="submit" class="button-tertiary">
                        <x-svg.check class="text-white" />
                        {{ $isEditMode ? __('Mettre à jour') : __('Créer') }}
                    </button>
                </x-modal.footer>
            </form>
        </x-modal.panel>
    </x-modal>
</div>
