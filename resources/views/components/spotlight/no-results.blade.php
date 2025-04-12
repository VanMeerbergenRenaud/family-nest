@props(['search'])

<div class="flex items-center justify-start gap-3 py-3 px-4.5 border-slate-200">
    <p class="text-sm-medium text-gray-500 dark:text-gray-400">
        Aucun résultat pour "{{ $search }}"
    </p>
    <p class="text-xs text-gray-600 dark:text-gray-500">
        Essayez d'autres mots-clés ou vérifiez l'orthographe.
    </p>
</div>
<div class="bg-white dark:bg-gray-800 py-3 px-2">
    <p class="text-sm-medium text-gray-500 dark:text-gray-400 mb-2 pl-2.5">
        Suggestions
    </p>
    <ul role="list" class="flex flex-col gap-1">
        <x-spotlight.result-item
            id="create-invoice"
            type="suggestion"
            :href="route('invoices.create')"
            text="Ajouter une nouvelle facture"
            shortcut="⌘ X"
        >
            <x-svg.add2 class="h-4 w-4 text-gray-400 dark:text-gray-500 group-hover:text-gray-800"/>
        </x-spotlight.result-item>

        <x-spotlight.result-item
            id="archived-invoices"
            type="suggestion"
            :href="route('invoices.archived')"
            text="Voir mes factures archivées"
        >
            <x-svg.archive class="h-4 w-4 text-gray-400 dark:text-gray-500 group-hover:text-gray-800"/>
        </x-spotlight.result-item>
    </ul>
</div>
