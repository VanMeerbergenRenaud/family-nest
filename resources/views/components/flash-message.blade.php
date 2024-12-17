<div class="fixed top-0 right-0 bottom-0 left-0 flex justify-end items-start p-4 pointer-events-none z-10">
    <div
        class="max-w-80 w-full bg-white rounded-xl pointer-events-auto"
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3000)"
        x-transition:enter="transition ease-out duration-300"
    >
        <div class="flex justify-between items-center gap-x-2 p-4 rounded-md shadow-md overflow-hidden">
            <div class="ml-2 w-4 h-4">
                @if($icon === 'delete')
                    <x-svg.trash />
                @elseif($icon === 'success')
                    <x-svg.success />
                @elseif($icon === 'import')
                    <x-svg.import />
                @elseif($icon === 'add')
                    <x-svg.add />
                @elseif($icon === 'advertising')
                    <x-svg.advertising />
                @else
                    <x-svg.show />
                @endif
            </div>
            <p class="ml-[-0.5rem] text-sm leading-5 font-medium text-gray-900">
                {{ $title }}
            </p>
            <div class="ml-[-0.5rem] text-gray-500 outline-none" @click="show = false">
                <button type="button" wire:click="{{ $method }}" class="p-2 hover:text-gray-600 focus:text-gray-600">
                    <x-svg.cross />
                </button>
            </div>
        </div>
    </div>
</div>
