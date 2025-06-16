@props([
    'position' => 'center', // Options: 'center', 'top', 'bottom', 'top-right', 'bottom-right'
    'size' => 'default',    // Options: 'default', 'sm', 'lg', 'xl', 'full'
    'width' => null,        // Largeur personnalisée (ex: '500px', '50%')
    'height' => null,       // Hauteur personnalisée (ex: '600px', '80%')
])

@php
    // Définir les classes de positionnement
    $positionClasses = [
        'center' => 'items-center justify-center',
        'top' => 'items-start justify-center pt-4',
        'bottom' => 'items-end justify-center pb-4',
        'top-right' => 'items-start justify-end pt-4 pr-4',
        'center-right' => 'items-center justify-end pr-3',
        'bottom-right' => 'items-end justify-end pb-4 pr-4',
    ];

    // Définir les classes de taille
    $sizeClasses = [
        'default' => 'max-w-[40rem]',
        'sm' => 'max-w-[30rem]',
        'lg' => 'max-w-[60rem]',
        'xl' => 'max-w-[80rem]',
        'full' => 'max-w-full mx-4',
    ];

    // Appliquer les classes en fonction des props
    $positionClass = $positionClasses[$position] ?? $positionClasses['center'];
    $sizeClass = $width ? '' : ($sizeClasses[$size] ?? $sizeClasses['default']);

    // Préparer les styles personnalisés
    $customStyles = [];
    if ($width) {
        $customStyles[] = "width: $width";
        // Si une largeur personnalisée est définie, on ajoute aussi max-width: 100%
        $customStyles[] = "max-width: 100%";
    }
    if ($height) {
        $customStyles[] = "height: $height";
    }
    $styleAttr = !empty($customStyles) ? 'style="'.implode('; ', $customStyles).'"' : '';
@endphp

<div
    x-cloak
    x-dialog
    x-model="open"
    class="fixed inset-0 overflow-y-auto z-55"
    @click="document.querySelectorAll('video').forEach(v => v.pause())"
>
    <!-- Overlay -->
    <div x-dialog:overlay x-transition.opacity class="fixed top-0 right-0 bottom-0 left-0 backdrop-blur-[1px] bg-[rgba(0,0,0,0.25)] dark:bg-[rgba(0,0,0,0.5)]"></div>

    <!-- Panel -->
    <div @keydown.escape.window="open = false"
         class="relative min-h-full p-3 flex {{ $positionClass }}"
    >
        <div x-dialog:panel x-transition.in x-transition.out.opacity class="relative w-full {{ $sizeClass }} bg-white dark:bg-gray-800 rounded-xl shadow-[0_0_0.5rem_rgba(0,0,0,0.2)] dark:shadow-[0_0_0.5rem_rgba(255,255,255,0.1)] overflow-hidden" {!! $styleAttr !!}>

            <!-- Panel close button -->
            <div class="absolute top-0 right-0 p-4 z-60">
                <button type="button" @click="open = false" class="p-2.5 rounded-full flex--center text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-slate-300">
                    <x-svg.cross/>
                </button>
            </div>

            <!-- Panel content -->
            <div class="overflow-y-scroll scrollbar-hidden max-h-[96vh]">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
