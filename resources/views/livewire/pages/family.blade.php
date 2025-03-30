<div>
    @if($family)
        <div class="px-4">
            <h2 class="text-xl-semibold">Membres de la famille</h2>
            <p class="text-sm-regular text-gray-500">Gérez les membres de votre famille et leurs autorisations de compte ici.</p>
        </div>

        <section class="mt-6 w-full overflow-hidden bg-white dark:bg-gray-800 rounded-2xl border border-slate-200">
            {{-- En-tête --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="pl-1 text-lg-semibold mb-3 sm:mb-0 dark:text-white">
                    {{ $family->name }}
                    <span class="relative -top-0.5 ml-2 px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs-medium dark:bg-gray-700 dark:text-gray-200">
                       {{ $members->total() }}
                   </span>
                </h2>
                <div class="flex flex-wrap gap-2">
                    <x-menu>
                        <x-menu.button class="button-primary flex items-center">
                            <x-svg.filter/>
                            Filtres
                        </x-menu.button>
                        <x-menu.items class="mt-2 w-64">
                            <p class="px-2.5 py-2 text-sm-medium text-gray-700 dark:text-gray-400">Filtres</p>
                            <x-menu.divider/>
                            <x-menu.item>
                                <x-svg.in-progress class="w-4 h-4 transition-colors duration-200"/>
                                En cours
                            </x-menu.item>
                            <x-menu.divider/>
                            <x-menu.item wire:click="resetSort"
                                         class="flex items-center text-sm-medium text-slate-800 hover:bg-slate-100 transition-colors rounded">
                                <x-svg.reset/>
                                Réinitialiser
                            </x-menu.item>
                        </x-menu.items>
                    </x-menu>

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
                                <button wire:click="sortBy('name')" class="flex items-center">
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
                                <button wire:click="sortBy('permission')" class="flex items-center">
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
                                <button wire:click="sortBy('relation')" class="flex items-center">
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
                            <tr>
                                <td>
                                    <div class="flex items-center">
                                        <div class="mr-3 rounded">
                                            <img src="{{ $member->avatar ?? asset('img/img_placeholder.jpg') }}"
                                                 alt="{{ $member->name }}"
                                                 class="h-10 min-w-8 w-10 rounded-full object-cover">
                                        </div>
                                        <p class="flex flex-col">
                                           <span
                                               class="text-sm-medium text-gray-900 dark:text-gray-400">
                                               {{ ucfirst($member->name) }}
                                               @if($member->id === $currentUser)
                                                   <span class="ml-2 text-xs-medium text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-full">Vous</span>
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
                                        $permissionClass = match($member->pivot->permission) {
                                            'admin' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                            'editor' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'viewer' => 'bg-rose-50 text-rose-700 border-rose-100',
                                            default => 'bg-slate-50 text-slate-700 border-slate-100'
                                        };

                                        $permissionLabel = $isCurrentUser
                                            ? 'Responsable'
                                            : match($member->pivot->permission) {
                                                'admin' => 'Administrateur',
                                                'editor' => 'Éditeur',
                                                'viewer' => 'Lecteur',
                                                default => 'Membre'
                                            };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs-medium border {{ $permissionClass }}">
                                       {{ $permissionLabel }}
                                   </span>
                                </td>
                                <td>
                                    @php
                                        $relation =  $member->pivot->relation;

                                        // match relation
                                        $relation = match($relation) {
                                            'parent' => 'Parent',
                                            'spouse' => 'Conjoint(e)',
                                            'child' => 'Enfant',
                                            'sibling' => 'Frère/Sœur',
                                            'friend' => 'Ami(e)',
                                            'self' => 'Moi-même',
                                            default => ucfirst($relation)
                                        };
                                    @endphp
                                    {{ $relation }}
                                </td>
                                <td>
                                    @php
                                        $invoiceCount = $member->paidInvoices()->count();
                                        $urgentCount = $member->paidInvoices()->where('payment_status', 'late')->count();
                                        $upcomingCount = $member->paidInvoices()->where('payment_status', 'paid')->count();
                                        $inProgressCount = $member->paidInvoices()->where('payment_status', 'pending')->count();
                                    @endphp

                                    <div class="flex flex-wrap gap-1">
                                       <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm bg-gray-100 text-gray-800">
                                           {{ $invoiceCount }}
                                       </span>

                                        @if($urgentCount > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm bg-amber-100 text-amber-800">
                                               {{ $urgentCount }} urgences
                                           </span>
                                        @endif

                                        @if($upcomingCount > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm bg-blue-100 text-blue-800">
                                               {{ $upcomingCount }} à venir
                                           </span>
                                        @endif

                                        @if($inProgressCount > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm bg-green-100 text-green-800">
                                               {{ $inProgressCount }} en cours
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
                                            @if(!$isAdmin)
                                                <x-menu.item type="link" href="#">
                                                    <x-svg.show class="w-4 h-4 group-hover:text-gray-900"/>
                                                    {{ __('Voir le profil') }}
                                                </x-menu.item>
                                            @endif
                                            @if($member->id === $currentUser)
                                                <x-menu.item type="link" href="{{ route('settings.profile') }}">
                                                    <x-svg.edit class="w-4 h-4 group-hover:text-gray-900"/>
                                                    {{ __('Modifier mon profil') }}
                                                </x-menu.item>
                                            @endif
                                            @if($members->count() > 1 && $isAdmin)
                                                <x-menu.item type="link" href="#">
                                                    <x-svg.switch class="w-4 h-4 group-hover:text-gray-900"/>
                                                    {{ __('Changer de rôle') }}
                                                </x-menu.item>
                                            @endif
                                            @if($isAdmin)
                                                <x-menu.item wire:click="showDeleteFamilyMFormModal" class="group hover:text-red-500">
                                                    <x-svg.trash class="w-4 h-4 group-hover:text-red-500"/>
                                                    {{ __('Supprimer la famille') }}
                                                </x-menu.item>
                                            @endif
                                        </x-menu.items>
                                    </x-menu>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    @if($members->hasPages())
                        <div class="p-4 border-t border-slate-200">
                            {{ $members->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </section>
    @endif

    @if($showDeleteFamilyModal)
        <x-modal wire:model="showDeleteFamilyModal">
            <x-modal.panel>
                <form wire:submit.prevent="deleteFamily">
                    @csrf

                    <div x-data="{ confirmation: '' }">
                        <div class="flex gap-x-6 p-8">
                            <x-svg.advertising/>

                            <div>
                                <h3 role="heading" aria-level="3" class="mb-4 text-xl-semibold">
                                    {{ __('Supprimer la famille') }}
                                </h3>
                                <p class="mt-2 text-md-regular text-gray-500">
                                    {{ __('Êtes-vous sûre de vouloir supprimer toute la famille') }}
                                    <strong class="font-semibold"> {{ ucfirst($family->name) }}&nbsp;?</strong>
                                    {{ __('Toutes les données seront supprimées. Cette action est irréversible.') }}
                                </p>
                                <div class="mt-6 mb-2 flex flex-col gap-3">
                                    <label for="delete-definitely-invoice" class="text-sm-medium text-gray-800">
                                        {{ __('Veuillez tapper "CONFIRMER" pour confirmer la suppression.') }}
                                    </label>
                                    <input x-model="confirmation" placeholder="CONFIRMER" type="text"
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

    {{-- Ajout de la propriété pour le modal de création de famille --}}
    @if(!$family)
        <x-empty-state
            title="Aucune famille n'a été créée"
            description="Vous n'avez pas encore de famille ? Créez-en une pour commencer à gérer vos dépenses ensemble."
        >
            <button wire:click="openCreateFamilyModal" class="button-tertiary">
                <x-svg.add2 class="text-white" />
                Créer une famille
            </button>
            <button wire:click="showFamilyExemple" class="button-primary">
                <x-svg.help class="text-gray-900" />
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

    {{-- Modal pour créer une famille --}}
    @if($showCreateFamilyModal)
        <x-modal wire:model="showCreateFamilyModal">
            <x-modal.panel>
                <form wire:submit.prevent="createFamily">
                    @csrf

                    <div class="p-6">
                        <!-- Titre et Description -->
                        <h2 class="text-xl font-semibold">Créer une famille</h2>
                        <p class="text-gray-500 text-sm mt-1 mb-2">
                            Pour commencer à gérer vos dépenses ensemble
                        </p>

                        <x-divider />

                        <!-- Formulaire simple pour le nom de la famille -->
                        <div class="grid grid-cols-1 gap-4 mt-6 mb-4">
                            <x-form.field
                                label="Nom de la famille"
                                name="familyName"
                                model="familyName"
                                placeholder="Ex: Famille Dupont"
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
                            <button type="submit" class="button-primary">
                                {{ __('Valider le nom') }}
                                <x-svg.validate />
                            </button>
                        </div>
                    </x-modal.footer>
                </form>
            </x-modal.panel>
        </x-modal>
    @endif

    {{-- Modal pour ajouter un membre à la famille --}}
    @if($showAddMemberModal)
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
                                <h2 class="text-2xl font-semibold">Inviter un membre</h2>
                                <p class="text-gray-600 dark:text-gray-400 px-4 lg:px-12">
                                    Invitez un membre de votre famille ou un proche pour mieux gérer vos dépenses ensemble.
                                </p>
                            </div>

                            <!-- Formulaire d'ajout de membre -->
                            <div class="grid grid-cols-1 gap-4 my-4">

                                <x-form.field
                                    label="Adresse email"
                                    name="memberEmail"
                                    model="memberEmail"
                                    placeholder="exemple@gmail.com"
                                    type="email"
                                    :asterix="true"
                                />

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-form.select
                                        label="Rôle dans la famille"
                                        name="memberPermission"
                                        model="memberPermission"
                                        :asterix="true"
                                    >
                                        @foreach($availablePermissions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </x-form.select>

                                    <x-form.select
                                        label="Relation avec vous"
                                        name="memberRelation"
                                        model="memberRelation"
                                        :asterix="true"
                                    >
                                        @foreach($availableRelations as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </x-form.select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-modal.footer class="bg-white dark:bg-gray-800 border-t border-gray-400 dark:border-gray-700">
                        <div class="flex justify-end w-full gap-3">
                            <x-modal.close>
                                <button type="button" class="button-secondary">
                                    {{ __('Annuler') }}
                                </button>
                            </x-modal.close>
                            <button type="submit" class="button-primary gap-2">
                                <x-svg.send />
                                {{ __('Inviter') }}
                            </button>
                        </div>
                    </x-modal.footer>
                </form>
            </x-modal.panel>
        </x-modal>
    @endif

    {{-- Modal pour montrer l'envoi de mail en cours avec wire:target --}}
    <div wire:loading wire:target="sendInvitation" class="fixed left-0 right-0 mx-auto bottom-8 w-fit flex-center z-60">
        <div class="bg-white dark:bg-gray-800 rounded-full shadow-lg py-2 px-4 flex items-center space-x-3 border border-gray-100 dark:border-gray-700">
            <svg class="animate-spin h-5 w-5 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="pr-1 text-sm font-medium text-gray-700 dark:text-gray-300">Envoi de l'invitation en cours...</span>
        </div>
    </div>
</div>
