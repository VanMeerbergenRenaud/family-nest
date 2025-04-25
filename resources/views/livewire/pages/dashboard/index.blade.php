<div class="flex gap-4 flex-col">
    @if($allInvoicesOfFamily && auth()->user()->hasFamily())
        <div class="flex gap-4">
            <ul>
                <span class="block py-4">
                    Factures de la {{ $family->name }}<br>Total : {{ $allInvoicesOfFamily->count() }}
                </span>
                <li class="flex flex-col mb-4">
                    <p>Factures archivées : {{ $allInvoicesOfFamily->where('is_archived', true)->count() }}</p>
                    <p>Factures non archivées : {{ $allInvoicesOfFamily->where('is_archived', false)->count() }}</p>
                </li>
                @foreach($allInvoicesOfFamily as $invoice)
                    <li class="flex flex-col gap-2">
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="text-lg font-semibold text-gray-900">
                            {{ $invoice->name }}
                        </a>
                        <p class="text-sm text-gray-500">
                            {{ $invoice->created_at }}
                        </p>
                    </li>
                @endforeach
            </ul>

            <ul>
                <span class="block py-4">
                    Factures de {{ $user->name ?? 'inconnu' }}<br>Total : {{ $allInvoicesOfUser->count() }}
                </span>
                <li class="flex flex-col mb-4">
                    <p>Factures archivées : {{ $allInvoicesOfUser->where('is_archived', true)->count() }}</p>
                    <p>Factures non archivées : {{ $allInvoicesOfUser->where('is_archived', false)->count() }}</p>
                </li>
                @foreach($allInvoicesOfUser as $invoice)
                    <li class="flex flex-col gap-2">
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="text-lg font-semibold text-gray-900">
                            {{ $invoice->name }}
                        </a>
                        <p class="text-sm text-gray-500">
                            {{ $invoice->created_at }}
                        </p>
                    </li>
                @endforeach
            </ul>
            {{--Factures des autres membres de la famille --}}
            <ul>
                <span class="block py-4">
                    Factures des autres membres<br>Total : {{ $allInvoicesOfOtherUsers->count() }}
                </span>
                <li class="flex flex-col mb-4">
                    <p>Factures archivées : {{ $allInvoicesOfOtherUsers->where('is_archived', true)->count() }}</p>
                    <p>Factures non archivées : {{ $allInvoicesOfOtherUsers->where('is_archived', false)->count() }}</p>
                </li>
                @foreach($allInvoicesOfOtherUsers as $invoice)
                    <li class="flex flex-col gap-2">
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="text-lg font-semibold text-gray-900">
                            {{ $invoice->name }}
                        </a>
                        <p class="text-sm text-gray-500">
                            {{ $invoice->created_at }}
                        </p>
                    </li>
                @endforeach
            </ul>
            <ul>
                <p class="mb-4">
                    Tous les membres de la famille :
                </p>
                @foreach($family->users as $member)
                    <li class="flex flex-col">
                        <a href="{{ route('settings.profile') }}">
                            {{ $member->name }}
                        </a>
                        <p class="text-sm text-gray-500">
                            {{ $member->email }}
                        </p>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <x-empty-state
            title="Salut {{ $user->name ?? 'inconnu' }} !"
            description="Votre tableau de bord est vide. Vous n'avez pas encore d'objectifs, de thèmes ou de factures. Veuillez commencer par ajouter une facture !"
        >
            @if(!auth()->user()->hasFamily())
                <a href="{{ route('family') }}" class="button-tertiary" title="Vers la page des familles" wire:navigate>
                    <x-svg.add2 class="text-white"/>
                    Ajouter une famille
                </a>
            @else
                <a href="{{ route('invoices.create') }}" class="button-tertiary" title="Vers la page des factures" wire:navigate>
                    <x-svg.add2 class="text-white"/>
                    Ajouter une facture
                </a>
            @endif
            <button wire:click="showDashboardExemple" class="button-primary">
                <x-svg.help class="text-gray-900"/>
                Voir un exemple
            </button>
        </x-empty-state>

        @if($showDashboardExempleModal)
            <x-modal wire:model="showDashboardExempleModal">
                <x-modal.panel>
                    <video controls class="w-full h-full rounded-lg" autoplay muted>
                        <source src="{{ asset('video/exemple-archive.mp4') }}" type="video/mp4">
                        Votre navigateur ne supporte pas la vidéo prévue.
                    </video>
                </x-modal.panel>
            </x-modal>
        @endif
    @endif
</div>
