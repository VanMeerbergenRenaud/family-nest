<section class="mb-8">
    <h3 role="heading" aria-level="3" class="mb-3 pl-4 text-md-semibold text-gray-800 dark:text-white">Catégories</h3>

    <div class="flex overflow-x-scroll gap-4 scrollbar-hidden">
        @foreach([
            ['id' => 'favorites', 'title' => 'Favoris', 'icon' => 'folder', 'bgClass' => 'bg-gray-100', 'iconBg' => 'bg-gray-200','iconColor' => 'text-green-500'],
            ['id' => 'paid', 'title' => 'Payées', 'icon' => 'validate', 'bgClass' => 'bg-green-100', 'iconBg' => 'bg-green-200','iconColor' => 'text-green-500'],
            ['id' => 'unpaid', 'title' => 'Non payées', 'icon' => 'clock', 'bgClass' => 'bg-red-50', 'iconBg' => 'bg-red-100','iconColor' => 'text-red-500'],
            ['id' => 'late', 'title' => 'En retard', 'icon' => 'document', 'bgClass' => 'bg-yellow-50', 'iconBg' => 'bg-yellow-100','iconColor' => 'text-yellow-500'],
            ['id' => 'high_priority', 'title' => 'Priorités élevées', 'icon' => 'shield-exclamation', 'bgClass' => 'bg-orange-50', 'iconBg' => 'bg-orange-100','iconColor' => 'text-orange-500'],
            ['id' => 'last_week', 'title' => 'Semaine dernière', 'icon' => 'clock', 'bgClass' => 'bg-blue-50', 'iconBg' => 'bg-blue-100','iconColor' => 'text-blue-500'],
        ] as $folder)
            <button wire:click.prevent="openFolder('{{ $folder['id'] }}', '{{ $folder['title'] }}')"
                    class="inline-block p-5 pb-4 min-w-52 rounded-xl {{ $folder['bgClass'] }}"
            >
                <div class="flex flex-col text-left">
                    <div class="mb-3.5 p-3 rounded-lg w-fit {{ $folder['iconBg'] }}">
                        @if(View::exists('components.svg.' . $folder['icon']))
                            <x-dynamic-component :component="'svg.' . $folder['icon']" class="w-6 h-6 stroke-[1.6px] {{ $folder['iconColor'] }}"/>
                        @else
                            <x-svg.document class="w-6 h-6 {{ $folder['iconColor'] }}"/>
                        @endif
                    </div>
                    <h4 role="heading" aria-level="4" class="inline text-md-medium text-gray-900 dark:text-white">{{ $folder['title'] }}</h4>
                    <span class="text-sm-medium text-gray-500 dark:text-gray-400 mt-1">
                        {{ $folderStats[$folder['id']]['count'] }} Fichiers
                        @if($folderStats[$folder['id']]['count'] > 0)
                            • {{ $this->formatAmount($folderStats[$folder['id']]['amount'], $folderStats[$folder['id']]['currency']) }}
                        @endif
                    </span>
                </div>
            </button>
        @endforeach

        @if($archivedInvoices->count() > 0)
            <a href="{{ route('invoices.archived') }}" class="inline-block p-5 pb-4 min-w-52 rounded-xl bg-teal-100">
                <div class="flex flex-col text-left">
                    <div class="mb-3.5 p-3 rounded-lg w-fit bg-teal-200">
                        <x-svg.archive class="w-6 h-6 text-teal-500 stroke-[1.6px]" />
                    </div>
                    <span class="text-md-medium text-gray-900">Archives</span>
                    <span class="text-sm-medium text-gray-500 mt-1">{{ $archivedInvoices->count() }} Fichiers</span>
                </div>
            </a>
        @endif
    </div>

    {{-- Modales --}}
    @if($showFolderModal)
        <x-modal wire:model="showFolderModal">
            <x-modal.panel>
                <!-- En-tête avec titre et compteur -->
                <div class="sticky top-0 px-6 py-5 flex items-center justify-between border-b border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 z-20">
                    <div class="flex flex-wrap items-center gap-3 pr-8">
                        <h2 class="text-xl-semibold text-gray-900 dark:text-white">{{ $folderTitle }}</h2>
                        <span class="inline-flex px-2.5 py-1 text-xs-medium rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                            {{ count($folderInvoices) }} {{ __('factures') }}
                        </span>
                    </div>
                </div>

                <!-- Contenu principal -->
                <div class="p-6 bg-gray-50 dark:bg-gray-950 overflow-y-auto min-h-[40vh] max-h-[60vh]">
                    @if($folderInvoices->isEmpty())
                        <!-- État vide -->
                        <div class="flex-center flex-col py-16 rounded-lg bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
                            <x-svg.document class="w-14 h-14 text-gray-300 dark:text-gray-700 mb-3"/>
                            <p class="text-center px-4 text-md-medium text-gray-700 dark:text-gray-300">{{ __('Aucune facture dans cette catégorie') }}</p>
                            <p class="text-center px-4 text-sm text-gray-500 dark:text-gray-500 mt-1">{{ __('Les factures que vous ajouterez apparaîtront ici !') }}</p>
                        </div>
                    @else
                        <!-- Liste des factures -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            @foreach($folderInvoices as $invoice)
                                <div wire:key="folder-invoice-{{ $invoice->id }}" class="bg-white dark:bg-gray-900 rounded-xl border border-slate-200 dark:border-gray-800 hover:shadow-xs transition-shadow duration-200 overflow-hidden">
                                    <!-- En-tête de la carte -->
                                    <div class="px-4 py-3 dark:border-gray-800 flex justify-between items-center">
                                        @php
                                            $extension = $invoice->file->file_extension ?? null;
                                        @endphp

                                        <div class="flex items-center gap-2.5 overflow-hidden pr-2">
                                            <div class="flex-shrink-0 p-1.5 rounded">
                                                @if(View::exists('components.svg.file.' . $extension))
                                                    <x-dynamic-component :component="'svg.file.' . $extension" class="w-5 h-5"/>
                                                @else
                                                    <x-svg.file.default class="w-5 h-5"/>
                                                @endif
                                            </div>
                                            <h3 class="font-medium text-gray-900 dark:text-white truncate">
                                                {{ $invoice->name }}
                                            </h3>
                                        </div>

                                        <x-invoices.menu-actions :$invoice />
                                    </div>

                                    <!-- Corps de la carte -->
                                    <div class="p-4 border-t border-b border-slate-100">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="space-y-1">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Date d\'émission') }}</p>
                                                <p class="text-sm-medium text-gray-900 dark:text-white">
                                                    {{ $invoice->dateForHumans($invoice->issued_date) }}
                                                </p>
                                            </div>
                                            <div class="space-y-1">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Montant') }}</p>
                                                <p class="text-sm-semibold text-gray-900 dark:text-white">
                                                    {{ $this->formatAmount($invoice->amount, $invoice->currency) }}
                                                </p>
                                            </div>
                                            <div class="space-y-1">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Date d’échéance') }}</p>
                                                <p class="text-sm-medium text-gray-900 dark:text-white">
                                                    {{ $invoice->dateForHumans($invoice->payment_due_date) }}
                                                </p>
                                            </div>
                                            <div class="space-y-1">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Émetteur') }}</p>
                                                <p class="text-sm-medium text-gray-900 dark:text-white truncate"
                                                   title="{{ $invoice->issuer_name ?? __('Non spécifié') }}">
                                                    {{ $invoice->issuer_name ?? __('Non spécifié') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pied de la carte -->
                                    <div class="dark:border-gray-800 px-4 py-3 flex justify-between items-center">
                                        @php
                                            $statusEnum = $invoice->payment_status;
                                            $statusColor = $statusEnum?->color() ?? 'gray';
                                            $statusClass = "bg-$statusColor-100 text-$statusColor-800";
                                            $statusText = $statusEnum?->label() ?? __('Non spécifié');
                                            $statusEmoji = $statusEnum?->emoji() ?? '';
                                        @endphp
                                        <span class="px-3 py-1 {{ $statusClass }} rounded-full text-xs-medium">
                                            {{ $statusEmoji }}&nbsp;&nbsp;{{ $statusText }}
                                        </span>


                                        <div class="flex gap-1">
                                            <button wire:click="showInvoiceModal({{ $invoice->id }})"
                                                    class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                                    title="{{ __('Voir') }}">
                                                <x-svg.show class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
                                            </button>

                                            @can('update', $invoice)
                                                <a href="{{ route('invoices.edit', $invoice->id) }}"
                                                   wire:navigate
                                                   class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                                   title="{{ __('Aller vers la page d\'édition') }}">
                                                    <x-svg.edit class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Pied de modal -->
                <x-modal.footer
                    class="bg-white dark:bg-gray-900 border-t border-slate-200 dark:border-gray-800 px-6 py-4">
                    <div class="flex items-center justify-between w-full">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ count($folderInvoices) }} {{ __('factures trouvées') }}
                        </div>
                        <x-modal.close>
                            <button type="button"
                                    class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm-medium transition-colors">
                                {{ __('Fermer') }}
                            </button>
                        </x-modal.close>
                    </div>
                </x-modal.footer>
            </x-modal.panel>
        </x-modal>
    @endif

    <x-invoices.modal.prewiew :$showInvoicePreviewModal :$filePath :$fileExtension :$fileName />
    <x-invoices.modal.delete :$showDeleteFormModal :$filePath :$fileExtension :$fileName/>
</section>
