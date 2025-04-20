<div>
    {{-- Empty state when no family is created --}}
    @if(!$family)
        <x-empty-state
            title="Votre famille n'a pas encore été créée"
            description="Vous n'avez pas encore de famille ? Créez-en une pour commencer à gérer vos dépenses ensemble."
        >
            <button wire:click="openCreateFamilyModal" class="button-tertiary">
                <x-svg.add2 class="text-white"/>
                Créer votre famille
            </button>
            <button wire:click="showFamilyExemple" class="button-primary">
                <x-svg.help class="text-gray-900"/>
                Voir un exemple
            </button>
        </x-empty-state>

        @if($showFamilyExempleModal)
            <x-modal wire:model="showFamilyExempleModal">
                <x-modal.panel>
                    <video controls class="w-full h-full rounded-lg" autoplay muted>
                        <source src="{{ asset('video/exemple-archive.mp4') }}" type="video/mp4">
                        Votre navigateur ne supporte pas la vidéo prévue.
                    </video>
                </x-modal.panel>
            </x-modal>
        @endif
    @endif

    {{-- Table of family members --}}
    @if($family)
        <x-header
            title="{{ $family->name ?? __('Votre famille') }}"
            description="Gérez les membres de votre famille et leurs autorisations de compte ici."
        />

        {{-- Section des invitations en attente --}}
        @if($pendingInvitations->count() > 0 && $canEdit)
            <section class="mt-6 w-full overflow-hidden bg-white dark:bg-gray-800 rounded-2xl border border-slate-200">
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 border-b border-gray-200 dark:border-gray-700">
                    <h3 role="heading" aria-level="3" class="pl-1 text-lg-semibold mb-3 sm:mb-0 dark:text-white">
                        Invitations en attente
                        <span class="relative -top-0.5 ml-2 px-2 py-1 bg-pink-100 text-pink-800 rounded-full text-xs-medium dark:bg-amber-700 dark:text-amber-200">
                            {{ $pendingInvitations->count() }}
                        </span>
                    </h3>
                </div>

                <div class="w-full overflow-x-auto">
                    <table class="w-full">
                        <thead>
                        <tr>
                            <th scope="col">Email</th>
                            <th scope="col">Rôle</th>
                            <th scope="col">Relation</th>
                            <th scope="col">Date d'envoi</th>
                            <th scope="col">Expiration</th>
                            <th scope="col">Statut</th>
                            <th scope="col" class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pendingInvitations as $invitation)
                            <tr wire:key="invitation-{{ $invitation->id }}">
                                <td>
                                    <span class="text-sm font-medium">{{ $invitation->email }}</span>
                                </td>
                                <td>
                                    @php
                                        $permission = App\Enums\FamilyPermissionEnum::tryFrom($invitation->permission);
                                        $permissionClass = $permission ? $permission->cssClasses() : 'bg-slate-50 text-slate-700 border-slate-100';
                                        $permissionLabel = $permission ? $permission->label() : 'Membre';
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-md text-xs-medium border {{ $permissionClass }}">
                                            {{ $permissionLabel }}
                                        </span>
                                </td>
                                <td>
                                    @php
                                        $relation = App\Enums\FamilyRelationEnum::tryFrom($invitation->relation);
                                        $relationLabel = $relation ? $relation->label() : ucfirst($invitation->relation);
                                    @endphp
                                    {{ $relationLabel }}
                                </td>
                                <td>
                                    {{ $invitation->created_at->format('d/m/Y') }}
                                </td>
                                <td>
                                    @if($invitation->isExpired())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Expirée
                                        </span>
                                    @else
                                        {{ $invitation->expires_at->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td>
                                    @if($invitation->send_failed)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Échec d'envoi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Envoyée
                                        </span>
                                    @endif
                                </td>
                                <td class="grid justify-end text-right">
                                    <x-menu>
                                        <x-menu.button class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <x-svg.dots class="w-5 h-5 text-gray-500"/>
                                        </x-menu.button>
                                        <x-menu.items>
                                            <x-menu.item wire:click="resendInvitation({{ $invitation->id }})" wire:loading.attr="disabled">
                                                <x-svg.send class="group-hover:text-gray-900"/>
                                                {{ __('Renvoyer l\'invitation') }}
                                            </x-menu.item>
                                            {{-- Supprimer l'invitation --}}
                                            <x-menu.item wire:click="deleteInvitation({{ $invitation->id }})" class="group hover:text-red-500" wire:loading.attr="disabled">
                                                <x-svg.trash class="group-hover:text-red-500"/>
                                                {{ __('Supprimer l\'invitation') }}
                                            </x-menu.item>
                                        </x-menu.items>
                                    </x-menu>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <section class="mt-6 w-full overflow-hidden bg-white dark:bg-gray-800 rounded-2xl border border-slate-200">
            {{-- En-tête --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 pl-6 border-b border-gray-200 dark:border-gray-700">
                <h3 role="heading" aria-level="3" class="pl-1 text-lg-semibold mb-3 sm:mb-0 dark:text-white">
                    {{ __('Membres de la famille') }}
                    <span aria-hidden="true" class="relative -top-0.5 ml-2 px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs-medium dark:bg-gray-700 dark:text-gray-200">
                       {{ $members->total() }}
                   </span>
                </h3>
                <div class="flex flex-wrap gap-2">
                    {{-- Inviter un membre - Seulement Admin ou Editor peuvent inviter --}}
                    @if($isAdmin)
                        <button type="button" wire:click="addMember" class="button-tertiary">
                            <x-svg.add2 class="text-white"/>
                            {{ __("Ajouter un membre") }}
                        </button>
                    @endif
                </div>
            </div>

            @if($members->isEmpty())
                <div class="p-6 text-center border border-slate-200">
                    <p class="text-gray-500">{{ __('Aucun membre dans la famille pour le moment.') }}</p>
                </div>
            @else
                <div class="w-full overflow-x-auto">
                    <table class="w-full" aria-labelledby="tableTitle">
                        <thead>
                        <tr>
                            <th scope="col">
                                <button type="button" wire:click="sortBy('name')" class="flex items-center">
                                    <span>Nom du membre</span>
                                    @if ($sortField === 'name')
                                        <svg class="ml-2 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             xmlns="http://www.w3.org/2000/svg">
                                            @if ($sortDirection === 'desc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th scope="col">
                                <button type="button" wire:click="sortBy('permission')" class="flex items-center">
                                    <span>Rôle</span>
                                    @if ($sortField === 'permission')
                                        <svg class="ml-2 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             xmlns="http://www.w3.org/2000/svg">
                                            @if ($sortDirection === 'desc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th scope="col">
                                <button type="button" wire:click="sortBy('relation')" class="flex items-center">
                                    <span>Relation</span>
                                    @if ($sortField === 'relation')
                                        <svg class="ml-2 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             xmlns="http://www.w3.org/2000/svg">
                                            @if ($sortDirection === 'desc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th scope="col">
                                <span>Factures associées</span>
                            </th>
                            <th scope="col" class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($members as $member)
                            <tr wire:key="member-{{ $member->id }}">
                                <td>
                                    <div class="flex items-center">
                                        <div class="mr-3 rounded">
                                            <img src="{{ $member->avatar_url ?? asset('img/img_placeholder.jpg') }}"
                                                 alt="{{ $member->name }}"
                                                 class="h-10 min-w-8 w-10 rounded-full object-cover">
                                        </div>
                                        <p class="flex flex-col">
                                           <span
                                               class="text-sm-medium text-gray-900 dark:text-gray-400">
                                               {{ $member->name ?? 'Nom inconnu' }}
                                               @if($member->id === $currentUser)
                                                   <span class="ml-2 text-xs-medium text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded-full">
                                                       Vous
                                                   </span>
                                               @endif
                                           </span>
                                            <span class="text-sm-regular text-gray-500 dark:text-gray-400">
                                               {{ $member->email }}
                                           </span>
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $isCurrentUser = $member->id === $currentUser;
                                        $permission = App\Enums\FamilyPermissionEnum::tryFrom($member->permission);
                                        $permissionClass = $permission ? $permission->cssClasses() : 'bg-slate-50 text-slate-700 border-slate-100';
                                        $permissionLabel = $permission ? $permission->label() : 'Membre';
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-md text-xs-medium border {{ $permissionClass }}">
                                       {{ $permissionLabel }}
                                   </span>
                                </td>
                                <td>
                                    @php
                                        $relation = App\Enums\FamilyRelationEnum::tryFrom($member->relation);
                                        $relationLabel = $relation ? $relation->label() : $member->relation;
                                    @endphp
                                    {{ $relationLabel }}
                                </td>
                                <td>
                                    @php
                                        $invoiceData = $invoiceCounts[$member->id] ?? [
                                            'total' => 0,
                                            'late' => 0,
                                            'unpaid' => 0,
                                            'pending' => 0
                                        ];
                                    @endphp

                                    <div class="flex flex-wrap gap-1">
                                       <span
                                           class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm bg-gray-100 text-gray-800">
                                           {{ $invoiceData['total'] }}
                                       </span>

                                        @if($invoiceData['late'] > 0)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm bg-amber-100 text-amber-800">
                                               {{ $invoiceData['late'] }} urgent
                                           </span>
                                        @endif

                                        @if($invoiceData['unpaid'] > 0)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm bg-blue-100 text-blue-800">
                                               {{ $invoiceData['unpaid'] }} à venir
                                           </span>
                                        @endif

                                        @if($invoiceData['pending'] > 0)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm bg-green-100 text-green-800">
                                               {{ $invoiceData['pending'] }} en cours
                                           </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="grid justify-end text-right">
                                    <x-menu>
                                        <x-menu.button class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <x-svg.dots class="w-5 h-5 text-gray-500"/>
                                        </x-menu.button>
                                        <x-menu.items>
                                            @if($member->id === $currentUser)
                                                <p class="text-left text-xs-medium uppercase text-gray-500 pt-1 pb-2 px-2">
                                                    Mon profil
                                                </p>
                                                <x-menu.item wire:click="showUserProfile({{ $member->id }})" class="group">
                                                    <x-svg.show class="group-hover:text-gray-900"/>
                                                    {{ __('Voir') }}
                                                </x-menu.item>
                                                <x-menu.item type="link" href="{{ route('settings.profile') }}">
                                                    <x-svg.edit class="group-hover:text-gray-900"/>
                                                    {{ __('Modifier') }}
                                                </x-menu.item>
                                            @else
                                                <x-menu.item wire:click="showUserProfile({{ $member->id }})" class="group">
                                                    <x-svg.show class="group-hover:text-gray-900"/>
                                                    {{ __('Voir le profil') }}
                                                </x-menu.item>
                                            @endif

                                            {{-- Seul un admin peut changer les rôles --}}
                                            @if($isAdmin && $member->id !== $currentUser)
                                                <x-menu.divider/>

                                                <p class="text-left text-xs-medium uppercase text-gray-500 pt-1 pb-2 px-2">
                                                    Changement de rôle
                                                </p>

                                                {{-- Option: Administrateur --}}
                                                <x-menu.item
                                                    wire:click="changeRole({{ $member->id }}, '{{ App\Enums\FamilyPermissionEnum::Admin->value }}')"
                                                    class="group {{ $member->permission === App\Enums\FamilyPermissionEnum::Admin->value ? App\Enums\FamilyPermissionEnum::Admin->cssClasses() : '' }}"
                                                    wire:loading.attr="disabled"
                                                >
                                                    <x-svg.admin class="{{ $member->permission === App\Enums\FamilyPermissionEnum::Admin->value ? 'text-' . App\Enums\FamilyPermissionEnum::Admin->color() . '-700 group-hover:text-gray-900' : '' }}"/>
                                                    {{ App\Enums\FamilyPermissionEnum::Admin->label() }}
                                                </x-menu.item>

                                                {{-- Option: Éditeur --}}
                                                <x-menu.item
                                                    wire:click="changeRole({{ $member->id }}, '{{ App\Enums\FamilyPermissionEnum::Editor->value }}')"
                                                    class="group {{ $member->permission === App\Enums\FamilyPermissionEnum::Editor->value ? App\Enums\FamilyPermissionEnum::Editor->cssClasses() : '' }}"
                                                    wire:loading.attr="disabled"
                                                >
                                                    <x-svg.edit class="{{ $member->permission === App\Enums\FamilyPermissionEnum::Editor->value ? 'text-' . App\Enums\FamilyPermissionEnum::Editor->color() . '-700 group-hover:text-gray-900' : '' }}"/>
                                                    {{ App\Enums\FamilyPermissionEnum::Editor->label() }}
                                                </x-menu.item>

                                                {{-- Option: Lecteur --}}
                                                <x-menu.item
                                                    wire:click="changeRole({{ $member->id }}, '{{ App\Enums\FamilyPermissionEnum::Viewer->value }}')"
                                                    class="group {{ $member->permission === App\Enums\FamilyPermissionEnum::Viewer->value ? App\Enums\FamilyPermissionEnum::Viewer->cssClasses() : '' }}"
                                                    wire:loading.attr="disabled"
                                                >
                                                    <x-svg.show class="{{ $member->permission === App\Enums\FamilyPermissionEnum::Viewer->value ? 'text-' . App\Enums\FamilyPermissionEnum::Viewer->color() . '-700 group-hover:text-gray-900' : '' }}"/>
                                                    {{ App\Enums\FamilyPermissionEnum::Viewer->label() }}
                                                </x-menu.item>

                                                <x-menu.divider/>

                                                <x-menu.item
                                                    wire:click="deleteMember({{ $member->id }})"
                                                    class="group hover:text-red-500"
                                                    wire:loading.attr="disabled"
                                                >
                                                    <x-svg.trash class="group-hover:text-red-500"/>
                                                    {{ __('Supprimer le membre') }}
                                                </x-menu.item>
                                            @endif

                                            {{-- Seul un admin peut gérer la famille --}}
                                            @if($isAdmin && $member->id === $currentUser)
                                                <x-menu.divider/>

                                                <p class="text-left text-xs-medium uppercase text-gray-500 py-1 px-2">
                                                    Ma famille
                                                </p>
                                                {{-- Modifier le nom de la famille --}}
                                                <x-menu.item wire:click="showModifyFamilyNameFormModal" class="group">
                                                    <x-svg.edit class="group-hover:text-gray-900"/>
                                                    {{ __('Modifier le nom') }}
                                                </x-menu.item>
                                                {{-- Supprimer les membres (visible seulement s'il y a plus d'un membre) --}}
                                                @if($members->count() > 1)
                                                    <x-menu.item wire:click="showDeleteFamilyMFormModal" class="group hover:text-red-500">
                                                        <x-svg.trash class="group-hover:text-red-500"/>
                                                        {{ __('Supprimer les membres') }}
                                                    </x-menu.item>
                                                @endif
                                            @endif
                                        </x-menu.items>
                                    </x-menu>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    @if($members->hasPages())
                        <div class="py-2 px-4 border-t border-slate-200">
                            {{ $members->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </section>
    @endif

    {{-- Modal pour voir les informations du profil --}}
    @if($showUserProfilInfos && $selectedUser)
        <x-modal wire:model="showUserProfilInfos">
            <x-modal.panel>
                <!-- En-tête avec avatar et informations de base -->
                <div class="flex-center flex-col gap-1 mt-10 mb-6">
                    <img src="{{ $selectedUser->avatar_url ?? asset('img/img_placeholder.jpg') }}"
                         alt="{{ $selectedUser->name }}"
                         loading="lazy"
                         class="mb-4 relative h-24 w-24 rounded-full object-cover"
                    >
                    <h2 class="text-xl-semibold text-gray-900">{{ $selectedUser->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $selectedUser->email }}</p>
                </div>

                <!-- Informations détaillées du profil -->
                <div class="mx-6 pb-6 space-y-6">
                    <x-divider />
                    <!-- Rôle -->
                    <div class="mb-4 flex items-center justify-between pl-2">
                        <div class="flex items-center gap-2 text-gray-800">
                            <x-svg.user />
                            <span class="text-sm-medium">Rôle dans la famille</span>
                        </div>

                        @php
                            $permission = App\Enums\FamilyPermissionEnum::tryFrom($selectedUser->permission);
                            $permissionClass = $permission ? $permission->cssClasses() : 'bg-slate-50 text-slate-700 border-slate-100';
                            $permissionLabel = $permission ? $permission->label() : 'Membre';
                        @endphp

                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs-medium border {{ $permissionClass }}">
                            {{ $permissionLabel }}
                        </span>
                    </div>

                    <!-- Relation -->
                    <div class="flex items-center justify-between px-2">
                        <div class="flex items-center gap-2 text-gray-800">
                            <x-svg.user-plus />
                            <span class="text-sm-medium">Relation</span>
                        </div>

                        @php
                            $relation = App\Enums\FamilyRelationEnum::tryFrom($selectedUser->relation);
                            $relationLabel = $relation ? $relation->label() : $selectedUser->relation;
                        @endphp

                        <span class="text-sm-medium text-gray-500">{{ $relationLabel }}</span>
                    </div>

                    <!-- Date d'ajout -->
                    <div class="flex items-center justify-between px-2">
                        <div class="flex items-center gap-2 text-gray-800">
                            <x-svg.calendar />
                            <span class="text-sm-medium">Date d'ajout</span>
                        </div>
                        <span class="text-sm-medium text-gray-500">
                            {{ $this->dateForHumans($selectedUser->created_at) }}
                        </span>
                    </div>

                    <!-- Statistiques des factures -->
                    <div class="mb-3">
                        <div class="px-2 flex items-center gap-2 text-gray-800">
                            <x-svg.invoice />
                            <span class="text-sm-medium">Factures associées</span>
                        </div>

                        <div class="grid grid-cols-4 gap-3 mt-4">
                            <!-- Badge Total -->
                            <div class="flex flex-col items-center justify-center py-2.5 px-1 bg-gray-50 rounded-lg border border-gray-100">
                                <span class="text-lg font-semibold text-gray-800">{{ $selectedUserInvoiceCounts['total'] }}</span>
                                <span class="text-xs text-gray-500 mt-0.5">Total</span>
                            </div>

                            <!-- Badge Urgent -->
                            <div class="flex flex-col items-center justify-center py-2.5 px-1 bg-amber-50 rounded-lg border border-amber-100 {{ $selectedUserInvoiceCounts['late'] > 0 ? 'opacity-100' : 'opacity-40' }}">
                                <span class="text-lg font-semibold {{ $selectedUserInvoiceCounts['late'] > 0 ? 'text-amber-700' : 'text-gray-400' }}">
                                    {{ $selectedUserInvoiceCounts['late'] }}
                                </span>
                                <span class="text-xs {{ $selectedUserInvoiceCounts['late'] > 0 ? 'text-amber-600' : 'text-gray-400' }} mt-0.5">Urgent</span>
                            </div>

                            <!-- Badge À venir -->
                            <div class="flex flex-col items-center justify-center py-2.5 px-1 bg-blue-50 rounded-lg border border-blue-100 {{ $selectedUserInvoiceCounts['unpaid'] > 0 ? 'opacity-100' : 'opacity-40' }}">
                                <span class="text-lg font-semibold {{ $selectedUserInvoiceCounts['unpaid'] > 0 ? 'text-blue-700' : 'text-gray-400' }}">
                                    {{ $selectedUserInvoiceCounts['unpaid'] }}
                                </span>
                                <span class="text-xs {{ $selectedUserInvoiceCounts['unpaid'] > 0 ? 'text-blue-600' : 'text-gray-400' }} mt-0.5">À venir</span>
                            </div>

                            <!-- Badge En cours -->
                            <div class="flex flex-col items-center justify-center py-2.5 px-1 bg-green-50 rounded-lg border border-green-100 {{ $selectedUserInvoiceCounts['pending'] > 0 ? 'opacity-100' : 'opacity-40' }}">
                                <span class="text-lg font-semibold {{ $selectedUserInvoiceCounts['pending'] > 0 ? 'text-green-700' : 'text-gray-400' }}">
                                    {{ $selectedUserInvoiceCounts['pending'] }}
                                </span>
                                <span class="text-xs {{ $selectedUserInvoiceCounts['pending'] > 0 ? 'text-green-600' : 'text-gray-400' }} mt-0.5">En cours</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pied de page avec ombre subtile -->
                <x-modal.footer>
                    <x-modal.close>
                        <button type="button" class="button-secondary">
                            {{ __('Fermer') }}
                        </button>
                    </x-modal.close>
                </x-modal.footer>
            </x-modal.panel>
        </x-modal>
    @endif

    {{-- Modal pour supprimer les membres de la famille --}}
    @if($showDeleteMembersModal && $isAdmin)
        <x-modal wire:model="showDeleteMembersModal">
            <x-modal.panel>
                <form wire:submit.prevent="deleteMembers">
                    @csrf

                    <div x-data="{ confirmation: '' }">
                        <div class="flex gap-x-6 p-8">
                            <x-svg.advertising/>

                            <div>
                                <h2 role="heading" aria-level="2" class="mb-4 text-xl-semibold">
                                    {{ __('Supprimer les membres de la famille') }}
                                </h2>
                                <p class="mt-2 text-md-regular text-gray-500">
                                    {{ __('Êtes-vous sûr de vouloir supprimer tous les membres de la famille') }}
                                    <strong class="font-semibold"> {{ $family->name ?? 'Nom de la famille' }}&nbsp;?</strong>
                                    {{ __('Tous les membres seront détachés de la famille, sauf vous. Cette action est irréversible.') }}
                                </p>
                                <div class="mt-6 mb-2 flex flex-col gap-3">
                                    <label for="delete-definitely-members" class="text-sm-medium text-gray-800">
                                        {{ __('Veuillez tapper "CONFIRMER" pour confirmer la suppression.') }}
                                    </label>
                                    <input x-model="confirmation" placeholder="CONFIRMER" type="text"
                                           class="py-2 px-3 text-sm-regular border border-gray-300 rounded-md w-[87.5%]"
                                           autofocus
                                    >
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
                                <button type="submit" class="button-danger" :disabled="confirmation !== 'CONFIRMER'"
                                        wire:loading.attr="disabled">
                                    {{ __('Supprimer') }}
                                </button>
                            </x-modal.close>
                        </x-modal.footer>
                    </div>
                </form>
            </x-modal.panel>
        </x-modal>
    @endif

    {{-- Modal pour modifier le nom de la famille --}}
    @if($showModifyFamilyNameModal && $isAdmin)
        <x-modal wire:model="showModifyFamilyNameModal">
            <x-modal.panel>
                <form wire:submit.prevent="updateFamilyName">
                    @csrf

                    <div class="p-6">
                        <!-- Titre et Description -->
                        <h2 role="heading" aria-level="2" class="text-xl-semibold">Modifier le nom de la famille</h2>
                        <p class="text-gray-500 text-sm mt-1 mb-2">
                            Changez le nom de votre famille
                        </p>

                        <x-divider/>

                        <!-- Formulaire pour le nouveau nom de la famille -->
                        <div class="grid grid-cols-1 gap-4 mt-6 mb-4">
                            <x-form.field
                                label="Nouveau nom de la famille"
                                name="newFamilyName"
                                model="form.newFamilyName"
                                placeholder="Exemple: Famille Dupont"
                                :asterix="true"
                                autofocus
                            />
                        </div>
                    </div>

                    <x-modal.footer class="bg-white dark:bg-gray-800 border-t border-gray-400 dark:border-gray-700">
                        <div class="flex justify-end w-full gap-3">
                            <x-modal.close>
                                <button type="button" class="button-primary">
                                    {{ __('Annuler') }}
                                </button>
                            </x-modal.close>
                            <button type="button" wire:click="updateFamilyName" class="button-secondary" wire:loading.attr="disabled">
                                {{ __('Enregistrer') }}
                                <x-svg.validate class="text-white" />
                            </button>
                        </div>
                    </x-modal.footer>
                </form>
            </x-modal.panel>
        </x-modal>
    @endif

    {{-- Modal to create a family --}}
    @if($showCreateFamilyModal)
        <x-modal wire:model="showCreateFamilyModal">
            <x-modal.panel>
                <form wire:submit.prevent="createFamily">
                    @csrf

                    <div class="p-6">
                        <!-- Titre et Description -->
                        <h2 role="heading" aria-level="2" class="text-xl font-semibold">Créer une famille</h2>
                        <p class="text-gray-500 text-sm mt-1 mb-2">
                            Pour commencer à gérer vos dépenses ensemble
                        </p>

                        <x-divider/>

                        <!-- Formulaire simple pour le nom de la famille -->
                        <div class="grid grid-cols-1 gap-4 mt-6 mb-4">
                            <x-form.field
                                label="Nom de la famille"
                                name="familyName"
                                model="form.familyName"
                                placeholder="Exemple: Famille Dupont"
                                :asterix="true"
                                autofocus
                            />
                        </div>
                    </div>

                    <x-modal.footer class="bg-white dark:bg-gray-800 border-t border-gray-400 dark:border-gray-700">
                        <div class="flex justify-end w-full gap-3">
                            <x-modal.close>
                                <button type="button" class="button-secondary">
                                    {{ __('Annuler') }}
                                </button>
                            </x-modal.close>
                            <button type="submit" class="button-primary" wire:loading.attr="disabled">
                                {{ __('Valider le nom') }}
                                <x-svg.validate/>
                            </button>
                        </div>
                    </x-modal.footer>
                </form>
            </x-modal.panel>
        </x-modal>
    @endif

    {{-- Modal to add a member to the family (accessible seulement si editor ou admin) --}}
    @if($showAddMemberModal && $isAdmin)
        <x-modal wire:model="showAddMemberModal">
            <x-modal.panel>
                <form wire:submit.prevent="sendInvitation">
                    @csrf

                    <div class="flex gap-x-6 p-6">
                        <div class="w-full space-y-6">
                            <!-- Titre et Description -->
                            <div class="text-center space-y-3">
                                <!-- Avatar Image -->
                                <div class="flex-center mt-4 mb-6">
                                    <img src="{{ asset('img/users/three.png') ?? null }}"
                                         alt="Exemple de 3 avatars"
                                         class="h-16 w-auto rounded-full object-cover bg-transparent">
                                </div>
                                <h2 role="heading" aria-level="2" class="text-2xl font-semibold">Inviter un membre</h2>
                                <p class="text-gray-600 dark:text-gray-400 px-4 lg:px-12">
                                    Invitez un membre de votre famille ou un proche pour mieux gérer vos dépenses
                                    ensemble.
                                </p>
                            </div>

                            <!-- Formulaire d'ajout de membre -->
                            <div class="grid grid-cols-1 gap-4 my-4">

                                <x-form.field
                                    label="Adresse email"
                                    name="memberEmail"
                                    model="form.memberEmail"
                                    placeholder="exemple@gmail.com"
                                    type="email"
                                    :asterix="true"
                                />

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-form.select
                                        label="Rôle dans la famille"
                                        name="memberPermission"
                                        model="form.memberPermission"
                                        :asterix="true"
                                    >
                                        {{-- Si admin, peut attribuer tous les rôles --}}
                                        @if($isAdmin)
                                            @foreach(App\Enums\FamilyPermissionEnum::getPermissionOptions() as $value => $label)
                                                <option value="{{ $value }}">
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                            {{-- Si editor, ne peut pas attribuer le rôle admin --}}
                                        @else
                                            @foreach(App\Enums\FamilyPermissionEnum::getPermissionOptions() as $value => $label)
                                                @if($value !== App\Enums\FamilyPermissionEnum::Admin->value)
                                                    <option value="{{ $value }}">
                                                        {{ $label }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </x-form.select>

                                    <x-form.select
                                        label="Relation avec vous"
                                        name="memberRelation"
                                        model="form.memberRelation"
                                        :asterix="true"
                                    >
                                        @foreach(App\Enums\FamilyRelationEnum::getRelationOptions() as $value => $label)
                                            <option value="{{ $value }}">
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </x-form.select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <x-modal.footer class="bg-white dark:bg-gray-800 border-t border-gray-400 dark:border-gray-700">
                        <div class="flex justify-end w-full gap-3">
                            <x-modal.close>
                                <button type="button" class="button-primary">
                                    {{ __('Annuler') }}
                                </button>
                            </x-modal.close>
                            <button type="submit" class="button-secondary gap-2" wire:loading.attr="disabled">
                                <x-svg.send class="text-white" />
                                {{ __('Inviter') }}
                            </button>
                        </div>
                    </x-modal.footer>
                </form>
            </x-modal.panel>
        </x-modal>
    @endif
</div>
