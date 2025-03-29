@props([
    'tags' => [],
    'tagInput' => '',
    'showSuggestions' => false,
    'suggestions' => []
])

<div class="mt-2">
    <label for="tags" class="relative mb-1.5 pl-2 block text-sm font-medium text-gray-800 dark:text-gray-200">
        Tags personnalisés
    </label>

    <div class="flex mt-2 relative">
        <input type="text"
               name="tags"
               id="tags"
               wire:model.live.debounce.300ms="form.tagInput"
               placeholder="Ajouter un tag..."
               class="flex-1 block w-full text-sm-regular rounded-l-md bg-white border border-slate-200 dark:border-gray-600 dark:text-white p-3 pl-4 focus:outline-0"
        >
        <button type="button" wire:click="addTag"
                class="inline-flex items-center px-4 py-2 text-sm-medium bg-white border border-l-0 border-slate-200 rounded-r-md hover:bg-gray-50 text-gray-700 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
            Ajouter un tag
        </button>

        <!-- Menu déroulant pour les suggestions -->
        @if($showSuggestions && count($suggestions) > 0)
            <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-10 max-h-60 overflow-y-auto dark:bg-gray-800 dark:border-gray-700">
                <ul class="p-1.5" x-data="{selectedIndex: -1}">
                    @foreach($suggestions as $index => $tag)
                        <li wire:key="tag-suggestion-{{ $index }}"
                            x-bind:class="{'bg-indigo-50 dark:bg-indigo-900': selectedIndex === {{ $index }}}"
                            wire:click="selectTag('{{ $tag }}')"
                            class="px-4 py-2 rounded-md text-sm-regular text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900 cursor-pointer flex items-center">
                            <x-svg.tag class="mr-2 text-indigo-500"/>
                            {{ $tag }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Tags ajoutés --}}
    @if(count($tags) > 0)
        <ul class="flex flex-wrap gap-2.5 mt-1.5 ml-2">
            @foreach($tags as $index => $tag)
                <li class="mt-2 inline-flex items-center pl-3.5 pr-2.5 pt-1 pb-1.5 rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                    <span class="text-sm-regular">{{ $tag }}</span>
                    <button type="button" wire:click="removeTag({{ $index }})"
                            class="relative top-0.25 ml-1.5 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">
                        <x-svg.cross class="h-4 w-4 text-indigo-700" />
                    </button>
                </li>
            @endforeach
        </ul>
    @endif
</div>
