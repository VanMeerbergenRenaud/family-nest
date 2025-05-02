@props([
    'target' => null,
])

<div wire:loading wire:target="{{ $target }}" class="absolute inset-0 bg-white opacity-50 z-80"></div>
<div wire:loading.flex wire:target="{{ $target }}" class="flex-center absolute inset-0 z-80">
    <x-svg.spinner class="text-gray-500 size-7" />
</div>
