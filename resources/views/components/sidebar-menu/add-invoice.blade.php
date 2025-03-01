@props([
    'expanded' => true,
])

<a href="{{ route('invoices.create') }}"
   title="Vers la page d'ajout d'une facture"
   class="font-semibold rounded-lg text-sm p-2.5 text-center flex items-center justify-center gap-3 text-white bg-purple-500 hover:bg-purple-600 dark:bg-gray-600 dark:hover:bg-gray-700"
   wire:navigate>
    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
    </svg>
    <span x-show="expanded" class="text-sm-medium">Ajouter une facture</span>
</a>
