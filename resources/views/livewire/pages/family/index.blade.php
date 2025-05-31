<div>
    {{-- Empty state when no family is created --}}
    @if(!$family)
        <x-empty-state
            title="Votre famille n'a pas encore été créée"
            description="Vous n'avez pas encore de famille ? Créez-en une pour commencer à gérer vos dépenses ensemble."
        >
            {{-- Si le user n'a pas de famille mais déjà des factures associées --}}
            @if(auth()->user()->invoices()->exists())
                <p class="flex gap-3 p-2 items-start text-sm text-blue-600 bg-blue-50 rounded-lg border border-blue-400">
                    <x-svg.info class="min-w-4 h-4 text-blue-400 mt-1"/>
                    Il semblerait que vous ayez déjà des factures associées à votre compte. Pour les gérer, vous devez
                    créer une nouvelle famille.
                </p>
            @endif
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
            title="{{ ('Famille ') . $family->name ?? __('Votre famille') }}"
            description="Gérez les membres de votre famille et leurs autorisations de compte ici."
        />

        {{-- Section des invitations en attente --}}
        @if($this->pendingInvitations->count() > 0 && $canEdit)
            <section class="mt-5 w-full overflow-hidden bg-white dark:bg-gray-800 rounded-2xl border border-slate-200">
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 border-b border-gray-200 dark:border-gray-700">
                    <h3 role="heading" aria-level="3" class="pl-1 text-lg-semibold mb-3 sm:mb-0 dark:text-white">
                        Invitations en attente
                        <span
                            class="relative -top-0.5 ml-2 px-2 py-1 bg-pink-100 text-pink-800 rounded-full text-xs-medium dark:bg-amber-700 dark:text-amber-200">
                            {{ $this->pendingInvitations->count() }}
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
                        @foreach($this->pendingInvitations as $invitation)
                            <tr wire:key="invitation-{{ $invitation->id }}">
                                <td>
                                    <span class="text-sm font-medium">{{ $invitation->email }}</span>
                                </td>
                                <td>
                                    @php
                                        $permission = $familyPermissionEnum::tryFrom($invitation->permission);
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
                                        $relation = $familyRelationEnum::tryFrom($invitation->relation);
                                        $relationLabel = $relation ? $relation->label() : ucfirst($invitation->relation);
                                    @endphp
                                    {{ $relationLabel }}
                                </td>
                                <td>
                                    {{ $invitation->created_at->format('d/m/Y') }}
                                </td>
                                <td>
                                    @if($invitation->isExpired())
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs-medium bg-red-100 text-red-800">
                                            Expirée
                                        </span>
                                    @else
                                        {{ $invitation->expires_at->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td>
                                    @if($invitation->send_failed)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs-medium bg-red-100 text-red-800">
                                            Échec d'envoi
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs-medium bg-green-100 text-green-800">
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
                                            <x-menu.item wire:click="resendInvitation({{ $invitation->id }})"
                                                         wire:loading.attr="disabled">
                                                <x-svg.send class="group-hover:text-gray-900"/>
                                                {{ __('Renvoyer l\'invitation') }}
                                            </x-menu.item>
                                            {{-- Supprimer l'invitation --}}
                                            <x-menu.item wire:click="deleteInvitation({{ $invitation->id }})"
                                                         class="group hover:text-red-500" wire:loading.attr="disabled">
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

        <section class="mt-5 w-full overflow-hidden bg-white dark:bg-gray-800 rounded-2xl border border-slate-200">
            {{-- En-tête --}}
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 pl-6 border-b border-gray-200 dark:border-gray-700">
                <h3 role="heading" aria-level="3" class="pl-1 text-lg-semibold mb-3 sm:mb-0 dark:text-white">
                    {{ __('Membres de la famille') }}
                    <span aria-hidden="true"
                          class="relative -top-0.5 ml-2 px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs-medium dark:bg-gray-700 dark:text-gray-200">
                       {{ $this->members->total() }}
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

            @if($this->members->isEmpty())
                <div class="p-6 text-center border border-slate-200">
                    <p class="text-gray-500">{{ __('Aucun membre dans la famille pour le moment.') }}</p>
                </div>
            @else
                <div class="relative w-full overflow-x-auto min-h-[70vh]  flex flex-col justify-between">
                    <table class="w-full" aria-labelledby="tableTitle">
                        <thead>
                        <tr>
                            <th scope="col">
                                <x-dashboard.sortable column="name" :$sortCol :$sortAsc>
                                    <span class="text-xs-semibold">Nom du membre</span>
                                </x-dashboard.sortable>
                            </th>
                            <th scope="col">
                                <x-dashboard.sortable column="permission" :$sortCol :$sortAsc>
                                    <span class="text-xs-semibold">Rôle</span>
                                </x-dashboard.sortable>
                            </th>
                            <th scope="col">
                                <x-dashboard.sortable column="relation" :$sortCol :$sortAsc>
                                    <span class="text-xs-semibold">Relation</span>
                                </x-dashboard.sortable>
                            </th>
                            <th scope="col">
                                <span class="text-xs-semibold">Factures associées</span>
                            </th>
                            <th scope="col" class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($this->members as $member)
                            <tr wire:key="member-{{ $member->id }}">
                                <td>
                                    <div class="flex items-center">
                                        <div class="mr-3 rounded">
                                            <img src="{{ $member->avatar_url ?? asset('img/img_placeholder.jpg') }}"
                                                 class="h-10 min-w-10 rounded-full object-cover"
                                                 alt="{{ $member->name }}"
                                                 loading="lazy"
                                            >
                                        </div>
                                        <p class="flex flex-col">
                                                   <span
                                                       class="text-sm-medium text-gray-900 dark:text-gray-400 capitalize">
                                                       {{ $member->name ?? 'Nom inconnu' }}
                                                       @if($member->id === $currentUser)
                                                           <span
                                                               class="ml-2 text-xs-medium text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded-full">
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
                                        $permission = $familyPermissionEnum::tryFrom($member->permission);
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
                                        $relation = $familyRelationEnum::tryFrom($member->relation);
                                        $relationLabel = $relation ? $relation->label() : $member->relation;
                                    @endphp
                                    {{ $relationLabel }}
                                </td>
                                <td>
                                    @php
                                        $invoiceData = $this->invoiceCounts[$member->id] ?? [
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
                                                    wire:click="changeRole({{ $member->id }}, '{{ $familyPermissionEnum::Admin->value }}')"
                                                    class="group {{ $member->permission === $familyPermissionEnum::Admin->value ? $familyPermissionEnum::Admin->cssClasses() : '' }}"
                                                    wire:loading.attr="disabled"
                                                >
                                                    <x-svg.admin class="{{ $member->permission === $familyPermissionEnum::Admin->value ? 'text-' . $familyPermissionEnum::Admin->color() . '-700 group-hover:text-gray-900' : '' }}"/>
                                                    {{ $familyPermissionEnum::Admin->label() }}
                                                </x-menu.item>

                                                {{-- Option: Éditeur --}}
                                                <x-menu.item
                                                    wire:click="changeRole({{ $member->id }}, '{{ $familyPermissionEnum::Editor->value }}')"
                                                    class="group {{ $member->permission === $familyPermissionEnum::Editor->value ? $familyPermissionEnum::Editor->cssClasses() : '' }}"
                                                    wire:loading.attr="disabled"
                                                >
                                                    <x-svg.edit class="{{ $member->permission === $familyPermissionEnum::Editor->value ? 'text-' . $familyPermissionEnum::Editor->color() . '-700 group-hover:text-gray-900' : '' }}"/>
                                                    {{ $familyPermissionEnum::Editor->label() }}
                                                </x-menu.item>

                                                {{-- Option: Lecteur --}}
                                                <x-menu.item
                                                    wire:click="changeRole({{ $member->id }}, '{{ $familyPermissionEnum::Viewer->value }}')"
                                                    class="group {{ $member->permission === $familyPermissionEnum::Viewer->value ? $familyPermissionEnum::Viewer->cssClasses() : '' }}"
                                                    wire:loading.attr="disabled"
                                                >
                                                    <x-svg.show class="{{ $member->permission === $familyPermissionEnum::Viewer->value ? 'text-' . $familyPermissionEnum::Viewer->color() . '-700 group-hover:text-gray-900' : '' }}"/>
                                                    {{ $familyPermissionEnum::Viewer->label() }}
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
                                                @if($this->members->count() > 1)
                                                    <x-menu.item wire:click="showDeleteFamilyMFormModal"
                                                                 class="group hover:text-red-500">
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
                        @if($this->members->count() <= 5)
                            <tr>
                                <td colspan="100%"></td>
                            </tr>
                        @endif
                        </tbody>
                    </table>

                    @if($this->members->hasPages())
                        <div class="py-2 px-4 border-t border-slate-200">
                            {{ $this->members->links() }}
                        </div>
                    @endif

                    <x-loader.spinner target="sortBy, search, previousPage, nextPage, gotoPage" />
                </div>
            @endif
        </section>
    @endif

    {{-- Modal pour supprimer les membres de la famille --}}
    <x-family.modal.delete-members :$showDeleteMembersModal :$isAdmin :$family />

    {{-- Modal pour modifier le nom de la famille --}}
    <x-family.modal.edit-name :$showModifyFamilyNameModal :$isAdmin />

    {{-- Modal to create a family --}}
    <x-family.modal.create :$showCreateFamilyModal />

    {{-- Modal to add a member to the family (accessible seulement si editor ou admin) --}}
    <x-family.modal.add-member :$showAddMemberModal :$isAdmin :$permissionOptions :$relationOptions />

    @if($showUserProfil && $selectedUser)
        <x-modal wire:model="showUserProfil">
            <x-modal.panel>
                <!-- En-tête avec avatar et informations de base -->
                <div class="flex-center flex-col gap-1 mt-10 mb-6">
                    <img src="{{ $selectedUser->avatar_url ?? asset('img/img_placeholder.jpg') }}"
                         class="mb-4 relative h-24 w-24 rounded-full object-cover"
                         alt="{{ $selectedUser->name }}"
                         loading="lazy"
                    >
                    <h2 class="text-xl-semibold text-gray-900">{{ $selectedUser->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $selectedUser->email }}</p>
                </div>

                <!-- Informations détaillées du profil -->
                <div class="mx-6 pb-6 space-y-6">
                    <x-divider/>
                    <!-- Rôle -->
                    <div class="mb-4 flex items-center justify-between pl-2">
                        <div class="flex items-center gap-2 text-gray-800">
                            <x-svg.user/>
                            <span class="text-sm-medium">Rôle dans la famille</span>
                        </div>

                        @php
                            $permission = $familyPermissionEnum::tryFrom($selectedUser->permission);
                            $permissionClass = $permission ? $permission->cssClasses() : 'bg-slate-50 text-slate-700 border-slate-100';
                            $permissionLabel = $permission ? $permission->label() : 'Membre';
                        @endphp

                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-md text-xs-medium border {{ $permissionClass }}">
                            {{ $permissionLabel }}
                        </span>
                    </div>

                    <!-- Relation -->
                    <div class="flex items-center justify-between px-2">
                        <div class="flex items-center gap-2 text-gray-800">
                            <x-svg.user-plus/>
                            <span class="text-sm-medium">Relation</span>
                        </div>

                        @php
                            $relation = $familyRelationEnum::tryFrom($selectedUser->relation);
                            $relationLabel = $relation ? $relation->label() : $selectedUser->relation;
                        @endphp

                        <span class="text-sm-medium text-gray-500">{{ $relationLabel }}</span>
                    </div>

                    <!-- Date d'ajout -->
                    <div class="flex items-center justify-between px-2">
                        <div class="flex items-center gap-2 text-gray-800">
                            <x-svg.calendar/>
                            <span class="text-sm-medium">Date d'ajout</span>
                        </div>
                        <span class="text-sm-medium text-gray-500">
                            {{ $this->dateForHumans($selectedUser->created_at) }}
                        </span>
                    </div>

                    <!-- Statistiques des factures -->
                    <div class="mb-3">
                        <div class="px-2 flex items-center gap-2 text-gray-800">
                            <x-svg.invoice/>
                            <span class="text-sm-medium">Factures associées</span>
                        </div>

                        <div class="grid grid-cols-4 gap-3 mt-4">
                            <!-- Badge Total -->
                            <div class="flex flex-col items-center justify-center py-2.5 px-1 bg-gray-50 rounded-lg border border-gray-100">
                                <span class="text-lg-semibold text-gray-800">{{ $selectedUserInvoiceCounts['total'] }}</span>
                                <span class="text-xs text-gray-500 mt-0.5">Total</span>
                            </div>

                            <!-- Badge Urgent -->
                            <div class="flex flex-col items-center justify-center py-2.5 px-1 bg-amber-50 rounded-lg border border-amber-100 {{ $selectedUserInvoiceCounts['late'] > 0 ? 'opacity-100' : 'opacity-40' }}">
                                <span class="text-lg-semibold {{ $selectedUserInvoiceCounts['late'] > 0 ? 'text-amber-700' : 'text-gray-400' }}">
                                    {{ $selectedUserInvoiceCounts['late'] }}
                                </span>
                                <span class="text-xs {{ $selectedUserInvoiceCounts['late'] > 0 ? 'text-amber-600' : 'text-gray-400' }} mt-0.5">Urgent</span>
                            </div>

                            <!-- Badge À venir -->
                            <div class="flex flex-col items-center justify-center py-2.5 px-1 bg-blue-50 rounded-lg border border-blue-100 {{ $selectedUserInvoiceCounts['unpaid'] > 0 ? 'opacity-100' : 'opacity-40' }}">
                                <span class="text-lg-semibold {{ $selectedUserInvoiceCounts['unpaid'] > 0 ? 'text-blue-700' : 'text-gray-400' }}">
                                    {{ $selectedUserInvoiceCounts['unpaid'] }}
                                </span>
                                <span class="text-xs {{ $selectedUserInvoiceCounts['unpaid'] > 0 ? 'text-blue-600' : 'text-gray-400' }} mt-0.5">À venir</span>
                            </div>

                            <!-- Badge En cours -->
                            <div class="flex flex-col items-center justify-center py-2.5 px-1 bg-green-50 rounded-lg border border-green-100 {{ $selectedUserInvoiceCounts['pending'] > 0 ? 'opacity-100' : 'opacity-40' }}">
                                <span class="text-lg-semibold {{ $selectedUserInvoiceCounts['pending'] > 0 ? 'text-green-700' : 'text-gray-400' }}">
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
</div>
