<div>
    <!-- En-tête avec filtres -->
    <div class="flex justify-between items-center flex-wrap gap-4 mt-2 md:px-4 mb-6">
        <div class="w-full">
            <h2 role="heading" aria-level="2" class="text-lg-semibold text-gray-800 dark:text-white mb-1">Factures archivées</h2>
            <p class="text-sm-regular text-gray-500 w-full">
                Les factures archivées n'apparaissent plus dans la liste des factures de votre tableau de bord.
            </p>
        </div>

        <div class="flex gap-3 items-center justify-between w-full">
            @if($this->hasFamily())
                <div class="border border-slate-200 rounded-lg p-1 flex gap-1">
                    @php
                        $types = [
                            'all' => 'Toutes',
                            'personal' => 'Personnelles'
                        ];
                    @endphp
                    @foreach($types as $type => $label)
                        <button
                            type="button"
                            wire:click="setFilterType('{{ $type }}')"
                            class="px-3 py-1.5 text-sm rounded-md {{ $filterType === $type ? 'bg-blue-500 text-white' : 'hover:bg-gray-100' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            @endif

            @if(!$archivedInvoices->isEmpty())
                <div class="flex gap-2 items-center">
                    {{-- Télécharger tout --}}
                    <button
                        type="button"
                        class="button-secondary"
                        wire:click="downloadArchivedFiles"
                    >
                        <x-svg.download class="mr-0.5 text-white"/>
                        {{ __('Télécharger tout') }}
                    </button>

                    {{-- Vider toutes les archives --}}
                    <form wire:submit.prevent="showDeleteAllInvoicesForm">
                        @csrf

                        <button type="submit" class="button-danger">
                            <x-svg.trash class="mr-0.5 text-white"/>
                            {{ __('Supprimer les archives') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- État vide -->
    @if($archivedInvoices->isEmpty())
        <div class="px-4">
            <div class="flex flex-col items-center justify-center bg-gray-50/60 border border-slate-200/80 rounded-xl p-8">
                <div class="mb-6 p-2 bg-gray-50 rounded-full border border-slate-200">
                    <x-svg.archive class="w-5 h-5 text-slate-400"/>
                </div>

                <h3 class="text-lg font-medium text-slate-800 mb-2.5">
                    @if($filterType === 'personal')
                        Aucune facture personnelle archivée
                    @else
                        Aucune facture archivée
                    @endif
                </h3>

                <p class="text-sm text-slate-500 text-center max-w-md mx-auto">
                    @if($filterType === 'personal')
                        Vous n'avez pas encore archivé de factures personnelles. Les factures archivées n'apparaissent plus
                        dans votre tableau de bord principal mais restent accessibles ici.
                    @else
                        Les éléments archivés contiennent les factures que vous avez retirées de votre vue principale.
                        Vous pouvez restaurer une facture archivée à tout moment depuis cette page.
                    @endif
                </p>
            </div>
        </div>
    @else
        <!-- Liste des factures par année -->
        <div class="relative space-y-4 px-4" x-data="{ openYear: {{ $currentYear }} }" x-transition>
            @foreach($invoicesByYear as $year => $invoices)
                <div class="border border-slate-200 rounded-lg overflow-hidden">
                    <!-- En-tête de l'année -->
                    <div
                        @click="openYear = openYear === '{{ $year }}' ? null : '{{ $year }}'"
                        class="bg-gray-50 p-4 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition-colors"
                    >
                        <div class="flex items-center gap-2">
                            <x-svg.calendar class="w-5 h-5 text-gray-500" />
                            <h3 class="text-lg font-semibold text-gray-800">{{ $year }}</h3>
                            <span class="bg-gray-200 text-gray-700 text-xs font-medium px-2.5 py-0.5 rounded-full ml-2">
                                {{ $invoices->count() }} facture{{ $invoices->count() > 1 ? 's' : '' }}
                            </span>
                        </div>
                        <x-svg.chevron-down
                            class="text-gray-600 transition-transform duration-300 mr-1"
                            x-bind:class="openYear === '{{ $year }}' ? 'rotate-180' : ''"
                        />
                    </div>

                    <!-- Tableau des factures de l'année -->
                    <div x-show="openYear === '{{ $year }}'" x-collapse>
                        <div class="border-t border-t-slate-200 w-full overflow-x-auto rounded-b-lg" wire:loading.class="opacity-50">
                            <table class="w-full">
                                <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Émetteur</th>
                                    <th>Montant</th>
                                    <th>Date d'archivage</th>
                                    @if($this->hasFamily() && $filterType === 'all')
                                        <th>Propriétaire</th>
                                    @endif
                                    <th class="text-right">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($invoices->sortByDesc('updated_at') as $invoice)
                                    <tr wire:key="invoice-{{ $invoice->id }}">
                                        <td>
                                            <div class="flex items-center">
                                                @php $extension = $invoice->file->file_extension ?? null; @endphp
                                                <div class="mr-3 p-2 rounded">
                                                    @if(View::exists('components.svg.file.' . $extension))
                                                        <x-dynamic-component :component="'svg.file.' . $extension" class="w-6 h-6"/>
                                                    @else
                                                        <x-svg.document class="w-6 h-6"/>
                                                    @endif
                                                </div>
                                                <span class="text-sm-medium text-gray-900 dark:text-gray-400">
                                                    {{ $invoice->name ?? 'Nom inconnu' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>{{ $invoice->issuer_name ?? 'Non défini' }}</td>
                                        <td>{{ number_format($invoice->amount, 2, ',', ' ') ?? '00.0' }} {{ $invoice->currency ?? '€' }}</td>
                                        <td>{{ $invoice->updated_at ? $invoice->updated_at->locale('fr_FR')->isoFormat('D MMMM YYYY') : 'Non définie' }}</td>

                                        @if($this->hasFamily() && $filterType === 'all')
                                            <td>
                                                @if($invoice->user_id === auth()->id())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Vous</span>
                                                @else
                                                    <span class="text-sm text-gray-600">{{ $invoice->user->name ?? 'Membre' }}</span>
                                                @endif
                                            </td>
                                        @endif

                                        <td class="text-right">
                                            <div class="flex justify-end">
                                                <x-menu>
                                                    <x-menu.button class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                                        <x-svg.dots class="w-5 h-5 text-gray-500" />
                                                    </x-menu.button>

                                                    <x-menu.items>
                                                        <x-menu.item type="link" href="{{ route('invoices.show', $invoice->id) }}">
                                                            <x-svg.binocular class="w-4 h-4 group-hover:text-gray-900"/>
                                                            {{ __('Voir en détail') }}
                                                        </x-menu.item>

                                                        @can('update', $invoice)
                                                            <x-menu.divider />

                                                            <x-menu.item wire:click="restoreInvoice({{ $invoice->id }})">
                                                                <x-svg.restore class="w-4 h-4 group-hover:text-gray-900"/>
                                                                {{ __('Restaurer') }}
                                                            </x-menu.item>
                                                        @endcan

                                                        @can('delete', $invoice)
                                                            <x-menu.item wire:click="showDeleteInvoiceForm({{ $invoice->id }})" class="group hover:text-red-500">
                                                                <x-svg.trash class="w-4 h-4 group-hover:text-red-500"/>
                                                                {{ __('Supprimer définitivement') }}
                                                            </x-menu.item>
                                                        @endcan
                                                    </x-menu.items>
                                                </x-menu>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Arcive loading spinners... --}}
            <div wire:loading wire:target="deleteDefinitelyInvoice, deleteDefinitelyAllInvoice" class="absolute inset-0 bg-white opacity-50"></div>
            <div wire:loading.flex wire:target="deleteDefinitelyInvoice, deleteDefinitelyAllInvoice" class="flex-center absolute inset-0">
                <x-svg.spinner class="text-gray-500 size-7"/>
            </div>
        </div>

        <!-- Message si aucune facture archivée trouvée -->
        @if($invoicesByYear->isEmpty())
            <div class="text-center py-12 bg-gray-50 rounded-lg border border-gray-200">
                <x-svg.archive class="w-12 h-12 text-gray-400 mx-auto"/>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Aucune facture archivée trouvée</h3>
                <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                    @if($filterType === 'personal')
                        Vous n'avez pas encore archivé de factures personnelles.
                    @else
                        Aucune facture archivée n'a été trouvée avec les filtres actuels.
                    @endif
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    @if($this->hasFamily() && $filterType !== 'all')
                        <button wire:click="setFilterType('all')" class="button-secondary">
                            <x-svg.show class="mr-1" />
                            {{ __('Voir toutes les archives') }}
                        </button>
                    @else
                        <a href="{{ route('invoices.index') }}" class="button-tertiary">
                            <x-svg.document class="text-white mr-1" />
                            {{ __('Voir les factures actives') }}
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Modales de confirmation -->
        @if($showDeleteFormModal)
            <x-modal wire:model="showDeleteFormModal">
                <x-modal.panel>
                    <form wire:submit.prevent="deleteDefinitelyInvoice">
                        @csrf
                        <div x-data="{ confirmation: '' }">
                            <div class="flex gap-x-6 p-8">
                                <x-svg.advertising/>
                                <div>
                                    <h3 role="heading" aria-level="3" class="mb-4 text-xl-semibold">
                                        {{ __('Supprimer la facture') }}
                                    </h3>
                                    <p class="mt-2 text-md-regular text-gray-500">
                                        {{ __('Êtes-vous sûr de vouloir supprimer la facture') }}
                                        <strong class="font-semibold"> {{ $form->name }}&nbsp;?</strong>
                                        {{ __('Toutes les données seront supprimées. Cette action est irréversible.') }}
                                    </p>
                                    <div class="mt-6 mb-2 flex flex-col gap-3">
                                        <label for="delete-definitely-invoice" class="text-sm-medium text-gray-800">
                                            {{ __('Veuillez taper "CONFIRMER" pour confirmer la suppression.') }}
                                        </label>
                                        <input x-model="confirmation" placeholder="CONFIRMER" type="text" id="delete-definitely-invoice"
                                               class="py-2 px-3 text-sm-regular border border-gray-300 rounded-md w-[87.5%]"
                                               autofocus>
                                    </div>
                                </div>
                            </div>

                            <x-modal.footer>
                                <x-modal.close>
                                    <button type="button" class="button-secondary">{{ __('Annuler') }}</button>
                                </x-modal.close>
                                <x-modal.close>
                                    <button type="submit" class="button-danger" :disabled="confirmation !== 'CONFIRMER'">
                                        {{ __('Supprimer') }}
                                    </button>
                                </x-modal.close>
                            </x-modal.footer>
                        </div>
                    </form>
                </x-modal.panel>
            </x-modal>
        @endif

        <!-- Modal de suppression de toutes les archives -->
        @if($showDeleteAllFormModal)
            <x-modal wire:model="showDeleteAllFormModal">
                <x-modal.panel>
                    <form wire:submit.prevent="deleteDefinitelyAllInvoice">
                        @csrf
                        <div x-data="{ confirmation: '' }">
                            <div class="flex gap-x-6 p-8">
                                <x-svg.advertising/>
                                <div>
                                    <h3 role="heading" aria-level="3" class="mb-4 text-xl-semibold">
                                        {{ __('Supprimer les archives') }}
                                    </h3>
                                    <p class="mt-2 text-md-regular text-gray-500">
                                        @php
                                            $message = 'Êtes-vous sûr de vouloir supprimer définitivement toutes les factures archivées';
                                            if($filterType === 'personal') $message .= ' personnelles';
                                        @endphp
                                        {{ __($message) }}<strong class="font-semibold">&nbsp;?</strong>
                                        {{ __('Toutes les données seront supprimées. Cette action est irréversible.') }}
                                    </p>
                                    <div class="mt-6 mb-2 flex flex-col gap-3">
                                        <label for="delete-definitely-all-invoices" class="text-sm-medium text-gray-800">
                                            {{ __('Veuillez taper "VIDER" pour confirmer la suppression.') }}
                                        </label>
                                        <input x-model="confirmation" placeholder="VIDER" type="text" id="delete-definitely-all-invoices"
                                               class="py-2 px-3 text-sm-regular border border-gray-300 rounded-md w-[87.5%]"
                                               autofocus>
                                    </div>
                                </div>
                            </div>

                            <x-modal.footer>
                                <x-modal.close>
                                    <button type="button" class="button-secondary">{{ __('Annuler') }}</button>
                                </x-modal.close>
                                <x-modal.close>
                                    <button type="submit" class="button-danger" :disabled="confirmation !== 'VIDER'">
                                        {{ __('Supprimer toutes les archives') }}
                                    </button>
                                </x-modal.close>
                            </x-modal.footer>
                        </div>
                    </form>
                </x-modal.panel>
            </x-modal>
        @endif

        <!-- Modal de téléchargement -->
        @if($showDownloadSelectionModal)
            <x-modal wire:model="showDownloadSelectionModal">
                <x-modal.panel>
                    <form wire:submit.prevent="downloadSelectedArchives">
                        @csrf

                        <div class="p-5">
                            <h2 role="heading" aria-level="2" class="text-xl-semibold mb-4">
                                {{ __('Télécharger les factures archivées') }}
                            </h2>

                            <x-divider />

                            <div class="mt-6 space-y-6">
                                @if($this->hasFamily())
                                    <x-form.select
                                        label="Sélectionner les factures à télécharger"
                                        name="download-member-select"
                                        model="selectedMemberId"
                                        placeholder="Sélectionner un membre"
                                    >
                                        <option value="all">
                                            {{ __('Toutes les factures archivées') . ' ('. $archivedInvoices->count() .')' }}
                                        </option>
                                        @foreach($familyMembers as $member)
                                            @php
                                                $archivedInvoicesCount = $member->invoices()->where('is_archived', true)->count();
                                            @endphp
                                            <option value="{{ $member->id }}" {{ $archivedInvoicesCount === 0 ? 'disabled' : '' }}>
                                                {{ __('Archives de') }} {{ $member->name }} ({{ $archivedInvoicesCount }})
                                            </option>
                                        @endforeach
                                    </x-form.select>
                                @endif
                            </div>
                        </div>

                        <x-modal.footer>
                            <x-modal.close>
                                <button type="button" class="button-primary">
                                    {{ __('Annuler') }}
                                </button>
                            </x-modal.close>
                            <button type="submit" class="button-secondary flex items-center">
                                <x-svg.download class="mr-1.5 text-white"/>
                                {{ __('Télécharger les archives') }}
                            </button>
                        </x-modal.footer>
                    </form>

                    <!-- Loading spinner pour le téléchargement -->
                    <div wire:loading.flex wire:target="downloadSelectedArchives" class="fixed inset-0 bg-black/40 z-60 flex-center">
                        <div class="bg-white rounded-lg p-8 max-w-md w-full flex flex-col items-center">
                            <x-svg.spinner class="text-blue-500 w-12 h-12 mb-4"/>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Préparation du téléchargement</h3>
                            <p class="text-sm text-gray-500 text-center">
                                Nous préparons vos fichiers pour le téléchargement. Cela peut prendre quelques instants selon le nombre de factures.
                            </p>
                        </div>
                    </div>
                </x-modal.panel>
            </x-modal>
        @endif
    @endif
</div>
