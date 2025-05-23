<div class="flex flex-col gap-5 lg:p-4">
    <div class="md:px-4 flex flex-col lg:flex-row justify-between items-start gap-4">

        <h2 role="heading" aria-level="2" class="display-xs-medium max-w-xs truncate">
            Facture&nbsp;:&nbsp;{{ $invoice->name }}
        </h2>

        <x-loader.spinner target="downloadInvoice, toggleFavorite, copyInvoice, restoreInvoice, archiveInvoice" position="fixed" />

        <!-- Actions -->
        <div class="flex flex-wrap lg:justify-end gap-2">
            <button
                type="button"
                class="button-primary"
                wire:click="downloadInvoice({{ $invoice->id }})"
                wire:loading.attr="disabled"
            >
                <x-svg.download />
                {{ __('Télécharger') }}
            </button>

            @can('addToFavorite', $invoice)
                <button
                    type="button"
                    class="button-primary group"
                    wire:click="toggleFavorite({{ $invoice->id }})"
                    wire:loading.attr="disabled"
                >
                    @if($invoice->is_favorite)
                        <x-svg.star class="text-gray-700 fill-current mr-0.5"/>
                        {{ __('Retirer des favoris') }}
                    @else
                        <x-svg.star class="text-gray-500 mr-0.5" />
                        {{ __('Ajouter aux favoris') }}
                    @endif
                </button>
            @endif

            @can('update', $invoice)
                <div class="inline-flex">
                    <a href="{{ route('invoices.edit', $invoice->id) }}"
                       class="button-primary rounded-r-none border-r-0"
                       wire:navigate
                    >
                        <x-svg.edit />
                        {{ __('Modifier') }}
                    </a>


                    <button type="button"
                            class="button-primary rounded-l-none"
                            wire:click="copyInvoice({{ $invoice->id }})"
                            wire:loading.attr="disabled"
                    >
                        <x-svg.copy />
                        {{ __('Dupliquer') }}
                    </button>
                </div>
            @endcan

            @can('delete', $invoice)
                <div class="flex items-center">
                    @if($invoice->is_archived)
                        <button
                            type="button"
                            class="button-primary rounded-r-none border-r-0"
                            wire:click="restoreInvoice({{ $invoice->id }})"
                            wire:loading.attr="disabled"
                        >
                            <x-svg.restore />
                            {{ __('Restaurer') }}
                        </button>
                    @else
                        <button
                            type="button"
                            class="button-danger border-white rounded-r-none border-r-0"
                            wire:click="archiveInvoice({{ $invoice->id }})"
                            wire:loading.attr="disabled"
                        >
                            <x-svg.archive class="text-white" />
                            {{ __('Archiver') }}
                        </button>
                    @endif

                    <x-divider :vertical="true" class="bg-red-600/80 max-h-[2.5rem]"/>

                    <button
                        type="button"
                        class="button-danger border-white rounded-l-none border-l-0"
                        wire:click="showDeleteForm({{ $invoice->id }})"
                        wire:loading.attr="disabled"
                    >
                        <x-svg.trash class="text-white"/>
                        {{ __('Supprimer') }}
                    </button>
                </div>
            @endcan
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="grid lg:grid-cols-5 gap-4">

        <!-- Aperçu du fichier -->
        <div class="lg:col-span-2">
            <div class="border border-slate-200 rounded-2xl overflow-hidden h-fit">
                <div class="relative h-[85vh] overflow-y-auto bg-gray-100">
                    <x-invoices.file-viewer
                        :filePath="$filePath"
                        :fileExtension="$fileExtension"
                        :fileName="$fileName"
                        class="min-h-full"
                    />
                </div>
            </div>
        </div>

        <!-- Détails de facturation -->
        <div class="lg:col-span-3">
            <div class="border border-slate-200 rounded-2xl overflow-hidden bg-white">
                <div class="h-full overflow-y-auto">

                    <!-- En-tête avec statut et montant -->
                    <div class="p-5 lg:pl-6.5">
                        <div class="flex items-start max-sm:items-center justify-between mb-4 gap-4">

                            <div class="flex flex-col gap-2">
                                <p class="text-xl-semibold text-gray-900">
                                    {{ $invoice->name }}
                                </p>

                                @if($invoice->reference)
                                    <p class="flex items-center text-sm text-gray-500">
                                        <span class="bg-gray-100 w-5 h-5 rounded-md flex-center mr-2">
                                            #
                                        </span>
                                        {{ $invoice->reference }}
                                    </p>
                                @endif
                            </div>

                            @if($paymentStatusEnum)
                                <span class="relative -top-4 lg:-top-1 -right-1 flex-center gap-1.5 px-4.5 py-2 bg-gray-100 rounded-lg">
                                    <span class="text-xs">{{ $paymentStatusEnum->emoji() }}</span>
                                    <span class="text-sm-medium text-gray-700">{{ $paymentStatusEnum->label() }}</span>
                                </span>
                            @endif
                        </div>

                        @if($invoice->amount)
                            <div class="mt-5 flex items-baseline">
                                <p class="display-sm-bold text-gray-900">
                                    {{ number_format($invoice->amount, 2, ',', ' ') }}
                                </p>
                                <span class="text-xl-regular ml-1 text-gray-600">{{ $currencySymbol }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Séparateur -->
                    <x-divider class="my-2 border-slate-200"/>

                    <!-- Contenu principal -->
                    <div class="px-5 py-4 space-y-6">

                        @php
                            $iconClass = 'p-1.5 rounded-md mr-0.5';
                        @endphp

                        <div class="flex flex-col sm:flex-row gap-4">

                            <!-- Type et catégorie -->
                            <div class="flex-1">
                                <h3 role="heading" aria-level="3" class="pl-3 text-xs-medium uppercase text-gray-500 mb-2">Type et catégorie</h3>
                                <div class="px-4 py-3 flex items-center bg-teal-50 rounded-lg border border-slate-200">
                                    <span class="{{ $iconClass }} bg-teal-100 text-teal-700">
                                        <x-svg.rectangle-stack />
                                    </span>
                                    <p class="text-sm-medium text-gray-800">
                                        {{ $invoice->type->value ?? 'Non spécifié' }} - {{ $invoice->category->value ?? 'Non spécifiée' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Fournisseur -->
                            <div class="flex-1">
                                <h3 role="heading" aria-level="3" class="pl-3 text-xs-medium uppercase text-gray-500 mb-2">Fournisseur</h3>

                                @if($invoice->issuer_website)
                                    <a href="{{ $invoice->issuer_website }}"
                                       target="_blank"
                                       title="Visiter le site de {{ $invoice->issuer_name }}"
                                       class="px-4 py-3 flex gap-0.5 rounded-lg group bg-cyan-50 border border-slate-200 hover:bg-cyan-100 transition"
                                    >
                                        <span class="{{ $iconClass }} h-fit bg-cyan-100 text-cyan-700 group-hover:bg-cyan-200 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                                            </svg>
                                        </span>
                                        <p class="flex flex-col gap-1 text-sm-medium text-gray-800">
                                            {{ $invoice->issuer_name ?? 'Non spécifié' }}
                                            <span class="flex items-center text-sm text-gray-500 group-hover:text-indigo-600 transition">
                                                {{ $invoice->issuer_website }}
                                            </span>
                                        </p>
                                    </a>
                                @else
                                    <div class="p-3 flex items-start gap-3 bg-cyan-50 rounded-lg border border-slate-200">
                                        <span class="bg-cyan-100 p-1.5 rounded-lg text-cyan-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>
                                            </svg>
                                        </span>
                                        <p class="flex flex-col text-sm-medium text-gray-800">
                                            {{ $invoice->issuer_name ?? 'Non spécifié' }}
                                            <span class="text-sm text-gray-500">
                                                Site non spécifié
                                            </span>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Dates -->
                        <div>
                            <h3 role="heading" aria-level="3" class="pl-3 text-xs-medium uppercase text-gray-500 mb-2">Dates importantes</h3>

                            <div class="bg-white rounded-lg border border-slate-200">
                                <div class="grid grid-cols-2 md:grid-cols-4">
                                    <div class="pl-4 pr-2 py-3 flex flex-col gap-2.5 md:border-r border-slate-200">
                                        <span class="text-xs text-gray-500">Émission</span>
                                        <span class="text-sm-medium text-gray-900 flex flex-wrap items-center gap-2">
                                            <span class="bg-blue-50 {{ $iconClass }} text-blue-600">
                                                <x-svg.calendar />
                                            </span>
                                            {{ $this->formatDate($invoice->issued_date) }}
                                        </span>
                                    </div>

                                    @if($invoice->payment_reminder)
                                        <div class="pl-4 pr-2 py-3 flex flex-col gap-2 md:border-r border-slate-200 border-t md:border-t-0">
                                            <span class="text-xs text-gray-500">Rappel</span>
                                            <span class="text-sm-medium text-gray-900 flex flex-wrap items-center gap-2">
                                                <span class="bg-rose-50 {{ $iconClass }} text-rose-600">
                                                    <x-svg.bell />
                                                </span>
                                                {{ $this->formatDate($invoice->payment_reminder) }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="pl-4 pr-2 py-3 flex flex-col gap-2 md:border-r border-slate-200">
                                        <span class="text-xs text-gray-500">Échéance</span>
                                        <span class="text-sm-medium text-gray-900 flex flex-wrap items-center gap-2">
                                            <span class="bg-amber-50 {{ $iconClass }} text-amber-600">
                                                <x-svg.clock />
                                            </span>
                                            {{ $this->formatDate($invoice->payment_due_date) }}
                                        </span>
                                    </div>

                                    @if($frequencyEnum)
                                        <div class="pl-4 pr-2 py-3 flex flex-col gap-2 border-t border-slate-200 md:border-t-0">
                                            <span class="text-xs text-gray-500">Fréquence</span>
                                            <span class="text-sm-medium text-gray-900 flex flex-wrap items-center gap-2">
                                                <span class="bg-indigo-50 {{ $iconClass }} text-indigo-600">
                                                    <x-svg.reset />
                                                </span>
                                                {{ $frequencyEnum->label() }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Informations de paiement -->
                        <div>
                            <h3 role="heading" aria-level="3" class="pl-3 text-xs-medium uppercase text-gray-500 mb-2">Informations de paiement</h3>
                            <div class="bg-white rounded-lg border border-slate-200">
                                <div class="grid grid-cols-1 sm:grid-cols-2">
                                    <div class="px-4 py-3 border-b sm:border-b-0 sm:border-r border-slate-200">
                                        <p class="text-xs text-gray-500 mb-2">Méthode</p>
                                        <p class="text-sm-medium text-gray-900 flex flex-wrap items-center gap-2">
                                            <span class="bg-purple-50 {{ $iconClass }} text-purple-700">
                                                <x-svg.credit-card />
                                            </span>
                                            {{ $paymentMethodEnum?->label() ?? 'Non spécifiée' }}
                                        </p>
                                    </div>
                                    <div class="px-4 py-3">
                                        <p class="text-xs text-gray-500 mb-2">Priorité</p>
                                        <p class="text-sm-medium text-gray-900 flex flex-wrap items-center gap-2">
                                            <span class="bg-blue-50 {{ $iconClass }} text-blue-700">
                                                <x-svg.shield-exclamation />
                                            </span>
                                            {{ $priorityEnum?->label() ?? 'Non spécifiée' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section de répartition des paiements -->
                        <div>
                            <h3 role="heading" aria-level="3" class="pl-3 text-xs-medium uppercase text-gray-500 mb-2">Répartition des paiements</h3>

                            @if($invoice->amount > 0)
                                <div class="px-4 py-3 space-y-4 rounded-lg bg-white border border-slate-200">
                                    <!-- Nom du payeur - toujours l'afficher -->
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500">Payeur :</span>
                                        <div class="flex items-center ml-2">
                                            <img src="{{ $payer->avatar_url ?? asset('img/img_placeholder.jpg') }}"
                                                 alt="{{ $payer->name ?? 'Non spécifié' }}"
                                                 class="w-6 h-6 object-cover rounded-full border-2 border-white"
                                                 loading="lazy"
                                            >
                                            <span class="ml-2 text-sm-medium text-gray-800">{{ $payer->name ?? 'Non spécifié' }}</span>
                                        </div>
                                    </div>

                                    <!-- Détail des parts - uniquement si réellement des parts dans la DB -->
                                    @if($hasShares)
                                        <div x-data="{ showRepartition: false }">
                                            <!-- En-tête avec bouton toggle -->
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 py-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm text-gray-500">Répartition :</span>
                                                    <span class="text-sm-medium text-teal-800">
                                                        {{ number_format($invoice->amount, 2, ',', ' ') }}&nbsp;{{ $currencySymbol }}
                                                    </span>
                                                </div>

                                                {{-- Pourcentage --}}
                                                <div class="flex items-center gap-2 flex-1">
                                                    <div class="relative h-1.5 bg-zinc-200 rounded-full flex-1 max-w-56">
                                                        <div class="h-1.5 rounded-full bg-teal-500"
                                                             style="width: {{ min($invoice->total_percentage, 100) }}%"></div>
                                                    </div>
                                                    <span class="text-xs-medium text-teal-800 bg-zinc-100 px-1.5 py-0.5 rounded">
                                                        {{ number_format($invoice->total_percentage) }}%
                                                    </span>
                                                </div>

                                                <button
                                                    type="button"
                                                    @click="showRepartition = !showRepartition"
                                                    class="flex items-center gap-1.5"
                                                >
                                                    <span
                                                        x-text="showRepartition ? 'Masquer détails' : 'Voir détails'"
                                                        class="text-sm-medium text-teal-700 hover:text-teal-900 transition-colors"
                                                    ></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.667"
                                                         class="h-4 w-4 transition-transform"
                                                         :class="showRepartition ? 'transform rotate-180' : ''"
                                                    >
                                                        <path d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Détail des répartitions - collapsible -->
                                            <ul x-show="showRepartition"
                                                x-collapse
                                                class="mt-3 pt-3 space-y-3 border-t border-teal-200"
                                            >
                                                @foreach($invoice->sharings as $sharing)
                                                    <li class="flex justify-between items-center gap-2 py-1"
                                                        wire:key="share-{{ $sharing->user_id }}">
                                                        <div class="flex items-center gap-2 sm:w-44 mr-2">
                                                            @php
                                                                $shareUser = $sharing->user;
                                                                $isPayer = $sharing->user_id === $invoice->paid_by_user_id;
                                                            @endphp

                                                            <img src="{{ $shareUser->avatar_url ?? asset('img/img_placeholder.jpg') }}"
                                                                 class="w-6 h-6 rounded-full inline-block"
                                                                 alt="" loading="lazy"
                                                            >
                                                            <span class="text-sm text-gray-700">{{ $shareUser->name }}</span>
                                                        </div>
                                                        <p class="text-sm-medium text-gray-800">
                                                            {{ $sharing->formatted_amount }}&nbsp;{{ $currencySymbol }}
                                                        </p>
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-12 h-1 bg-gray-100 rounded-full">
                                                                <div class="h-1 rounded-full {{ $isPayer ? 'bg-indigo-400' : 'bg-gray-400' }}"
                                                                     style="width: {{ min($sharing->share_percentage, 100) }}%"></div>
                                                            </div>
                                                            <span class="text-xs text-gray-500 ml-1">{{ $sharing->formatted_percentage }}%</span>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <div class="flex items-center">
                                            <span class="text-sm text-teal-700">Répartition :</span>
                                            <span class="ml-2 text-sm-medium text-gray-700">{{ __('Non définie') }}</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <!-- Montant non défini -->
                                <div class="bg-teal-50 rounded-xl border border-teal-100 p-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-teal-700 font-medium">Répartition</span>
                                        <span class="text-sm-medium text-gray-700">{{ __('Non spécifié') }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Note (si disponibles) -->
                        @if(!empty($invoice->notes))
                            <div>
                                <h3 role="heading" aria-level="3" class="pl-3 text-xs-medium uppercase text-gray-500 mb-2">Notes</h3>
                                <div class="px-4 py-3 bg-white rounded-lg border border-slate-200">
                                    <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $invoice->notes }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Tags (si disponibles) -->
                        @if(!empty($invoice->tags))
                            <div>
                                <h3 role="heading" aria-level="3" class="pl-3 text-xs-medium uppercase text-gray-500 mb-2">Tags</h3>
                                <ul class="pl-1 flex flex-wrap gap-2">
                                    @foreach($invoice->tags as $tag)
                                        <li wire:key="tag-{{ $tag }}">
                                            <span class="px-3 py-1.5 rounded-lg text-xs-medium bg-indigo-50 text-indigo-700 border border-indigo-100 flex items-center hover:bg-indigo-100 transition">
                                                <x-svg.tag2 class="w-3.5 h-3.5 mr-1.5 text-indigo-600" />
                                                {{ $tag }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modale de suppression -->
    <x-invoices.modal.delete :$showDeleteFormModal :$filePath :$fileExtension :$fileName/>
</div>
