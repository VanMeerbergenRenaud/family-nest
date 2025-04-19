<div>
    @if($archivedInvoices->isEmpty())
        <x-empty-state
            title="Aucune facture archivées pour le moment"
            description="Les élément de la corbeille contiennent les factures que vous avez initialement choisies d'archiver. Si, par malheur, vous souhaitez récupérer une facture que vous avez supprimée par inadvertance, c'est possible ici. Vous pouvez également supprimer définitivement une facture si vous le souhaitez, mais elle sera alors irrécupérable."
        >
            <a href="{{ route('invoices.index') }}" class="button-danger" title="Vers la page des factures">
                <x-svg.trash class="text-white" />
                {{ __('Archiver une facture') }}
            </a>
            <button wire:click="showArchiveExemple" class="button-primary">
                <x-svg.help class="text-gray-900" />
                Voir un exemple
            </button>
        </x-empty-state>

        @if($showArchiveExempleModal)
            <x-modal wire:model="showArchiveExempleModal">
                <x-modal.panel>
                    <video controls class="w-full h-full rounded-lg" autoplay muted>
                        <source src="{{ asset('video/exemple-archive.mp4') }}" type="video/mp4">
                        Votre navigateur ne supporte pas la vidéo prévue.
                    </video>
                </x-modal.panel>
            </x-modal>
        @endif
    @else
        <div class="flex justify-between items-center flex-wrap gap-4 mt-2 md:px-4 mb-6">
            <div>
                <h2 role="heading" aria-level="2" class="text-lg-semibold text-gray-800 dark:text-white mb-1">Factures archivées</h2>
                <p class="text-sm-regular text-gray-500 max-w-4xl">
                    Les éléments affichés ci-dessous sont des factures que vous avez archivées. Vous pouvez les restaurer ou les supprimer définitivement. Lorsqu'une facture est archivée elle n'apparaît plus dans la liste des factures de votre tableau de bord.
                </p>
            </div>

            @if(!$archivedInvoices->isEmpty())
                <button wire:click="showDeleteAllInvoicesForm" class="button-danger">
                    Vider la corbeille
                </button>
            @endif
        </div>

        <div class="w-full overflow-x-auto rounded-lg border border-gray" wire:loading.class="opacity-50">
            <table>
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Montant</th>
                    <th>Émetteur</th>
                    <th>Date d'archivage</th>
                    <th class="text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($archivedInvoices as $invoice)
                        <tr wire:key="invoice-{{ $invoice->id }}">
                            <td>
                                <div class="flex items-center">
                                    @php
                                        $extension = $invoice->file->file_extension ?? null;
                                    @endphp

                                    <div class="mr-3 p-2 rounded">
                                        @if(View::exists('components.svg.file.' . $extension))
                                            <x-dynamic-component :component="'svg.file.' . $extension" class="w-6 h-6"/>
                                        @else
                                            <x-svg.file.default class="w-6 h-6"/>
                                        @endif
                                    </div>

                                    <div class="flex flex-col">
                                        <span class="text-sm-medium text-gray-900 dark:text-gray-400">{{ $invoice->name ?? 'Non inconnu' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p>
                                    {{ number_format($invoice->amount, 2, ',', ' ') ?? '00.0' }} {{ $invoice->currency ?? '€' }}
                                </p>
                            </td>
                            <td>
                                <p>{{ $invoice->issuer_name ?? 'Non défini' }}</p>
                            </td>
                            <td>
                                <p>{{ $invoice->payment_due_date ? $invoice->payment_due_date->locale('fr_FR')->isoFormat('D MMMM YYYY') : 'Non définie' }}</p>
                            </td>
                            {{-- Actions --}}
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

                                            <x-menu.divider />

                                            <x-menu.item wire:click="restoreInvoice({{ $invoice->id }})">
                                                <x-svg.restore class="w-4 h-4 group-hover:text-gray-900"/>
                                                {{ __('Restaurer') }}
                                            </x-menu.item>

                                            <x-menu.item wire:click="showDeleteInvoiceForm({{ $invoice->id }})" class="group hover:text-red-500">
                                                <x-svg.trash class="w-4 h-4 group-hover:text-red-500"/>
                                                {{ __('Supprimer définitivement') }}
                                            </x-menu.item>
                                        </x-menu.items>
                                    </x-menu>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($archivedInvoices->hasPages())
            <div class="py-2 px-4 border-t border-slate-200">
                {{ $archivedInvoices->links() }}
            </div>
        @endif

        <!-- Modale pour supprimer d'une facture -->
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
                                        {{ __('Êtes-vous sûre de vouloir supprimer la facture') }}
                                        <strong class="font-semibold"> {{ $invoice->name }}&nbsp;?</strong>
                                        {{ __('Toutes les données seront supprimées. Cette action est irréversible.') }}
                                    </p>
                                    <div class="mt-6 mb-2 flex flex-col gap-3">
                                        <label for="delete-definitely-invoice" class="text-sm-medium text-gray-800">
                                            {{ __('Veuillez tapper "CONFIRMER" pour confirmer la suppression.') }}
                                        </label>
                                        <input x-model="confirmation" placeholder="CONFIRMER" type="text" id="delete-definitely-invoice"
                                               class="py-2 px-3 text-sm-regular border border-gray-300 rounded-md w-[87.5%]"
                                               autofocus>
                                    </div>
                                </div>
                            </div>

                            <x-modal.footer>
                                <x-modal.close>
                                    <button type="button" class="button-secondary">
                                        {{ __('Annuler') }}
                                    </button>
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

        <!-- Modale pour vider la corbeille -->
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
                                        {{ __('Vider la corbeille') }}
                                    </h3>
                                    <p class="mt-2 text-md-regular text-gray-500">
                                        {{ __('Êtes-vous sûr de vouloir supprimer définitivement toutes les factures archivées') }}
                                        <strong class="font-semibold">&nbsp;?</strong>
                                        {{ __('Toutes les données seront supprimées. Cette action est irréversible.') }}
                                    </p>
                                    <div class="mt-6 mb-2 flex flex-col gap-3">
                                        <label for="delete-definitely-all-invoices" class="text-sm-medium text-gray-800">
                                            {{ __('Veuillez tapper "VIDER" pour confirmer la suppression.') }}
                                        </label>
                                        <input x-model="confirmation" placeholder="VIDER" type="text" id="delete-definitely-all-invoices"
                                               class="py-2 px-3 text-sm-regular border border-gray-300 rounded-md w-[87.5%]"
                                               autofocus>
                                    </div>
                                </div>
                            </div>

                            <x-modal.footer>
                                <x-modal.close>
                                    <button type="button" class="button-secondary">
                                        {{ __('Annuler') }}
                                    </button>
                                </x-modal.close>

                                <x-modal.close>
                                    <button type="submit" class="button-danger" :disabled="confirmation !== 'VIDER'">
                                        {{ __('Vider la corbeille') }}
                                    </button>
                                </x-modal.close>
                            </x-modal.footer>
                        </div>
                    </form>
                </x-modal.panel>
            </x-modal>
        @endif
    @endif
</div>
