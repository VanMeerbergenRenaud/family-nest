<div class="relative">
    <div class="absolute pl-2.5 left-0.5 top-0 bottom-0 flex items-center pointer-events-none text-gray-500">
        <x-svg.magnifying-glass class="w-5 h-5 text-gray-400" />
    </div>

    <input wire:model.live.debounce.200ms="search" type="text" placeholder="Rechercher vos factures" class="block rounded-lg border border-slate-200 px-3 py-2.5 pl-10 min-w-56 text-sm-medium text-gray-900 placeholder:text-gray-400">
</div>
