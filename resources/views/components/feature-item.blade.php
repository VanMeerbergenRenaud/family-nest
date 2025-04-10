@props([
    'available' => true,
])

<li class="flex items-start">
    @if($available)
        <x-svg.success class="h-5 w-5 text-gray-500 mt-0.5 mr-2"/>
    @else
        <x-svg.error class="h-5 w-5 text-gray-400 mt-0.5 mr-2"/>
    @endif
    <span class="text-gray-600">{{ $slot }}</span>
</li>
