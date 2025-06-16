@props([
    'showDownloadSelectionModal' => false,
    'archivedInvoices' => null,
    'familyMembers' => null,
])

<div>
    <x-loader.spinner target="downloadSelectedArchives" />

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
            </x-modal.panel>
        </x-modal>
    @endif
</div>
