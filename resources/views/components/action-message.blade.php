@props(['on'])

<div x-data="{ actionMessage: false, timeout: null }"
     x-init="@this.on('{{ $on }}', () => {
        clearTimeout(timeout); actionMessage = true;
        timeout = setTimeout(() => { actionMessage = false }, 3000);
     })"
     x-show.transition.out.opacity.duration.1500ms="actionMessage"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95"
     style="display: none;"
     class="fixed top-0 right-0 m-0 p-6 bg-transparent"
    {{ $attributes->merge(['class' => '']) }}>
    <div class="max-w-sm p-2 rounded-xl bg-white border border-zinc-200">
        <div class="p-2 bg-white rounded-lg flex items-center justify-between space-x-4">
            {{ $slot->isEmpty() ? 'Sauvegardé.' : $slot }}
        </div>
    </div>
</div>
