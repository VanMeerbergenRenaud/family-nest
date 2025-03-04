@props([
    'expanded' => true,
])

<a href="{{ route('invoices.create') }}"
   title="Vers la page d'ajout d'une facture"
   class="font-semibold rounded-lg text-sm p-2.5 text-center flex items-center justify-center gap-3 text-white bg-purple-500 hover:bg-purple-600 dark:bg-gray-600 dark:hover:bg-gray-700"
   wire:navigate
>
    <x-svg.add class="w-4 h-4 text-white" />
    <span x-show="expanded" class="text-sm-medium">Ajouter une facture</span>
</a>
