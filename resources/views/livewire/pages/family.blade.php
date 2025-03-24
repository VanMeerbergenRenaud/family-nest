<div>
    <div>
        <h2 class="text-xl-semibold">Membres de la famille</h2>
        <p class="text-sm-regular text-gray-500">Gérez les membres de votre famille et leurs autorisations de compte
            ici.</p>
    </div>

    @if(!$family)
        <div class="mt-6 p-6 w-full overflow-hidden bg-white dark:bg-gray-800 rounded-2xl shadow-sm text-center">
            <p class="text-gray-500">{{ __('Vous n\'appartenez à aucune famille pour le moment.') }}</p>
            <button wire:click="createFamily" class="mt-4 button-tertiary">
                <x-svg.add2 class="text-white"/>
                Créer une famille
            </button>
        </div>
    @else
        <section class="mt-6 w-full overflow-hidden bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
            {{-- En-tête --}}
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 border-b border-gray-200 dark:border-gray-700">
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

                    <button wire:click="addAMember" class="button-tertiary">
                        <x-svg.add2 class="text-white"/>
                        Ajouter un membre
                    </button>
                </div>
            </div>

            @if($members->isEmpty())
                <div class="p-6 text-center">
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
                                <button wire:click="sortBy('role')" class="flex items-center">
                                    <span>Rôle</span>
                                    @if ($sortField === 'role')
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
                        {{-- Afficher tous les membres de la famille, y compris l'utilisateur --}}
                        @foreach($members as $member)
                            <tr class="{{ $member->id === $currentUser ? 'bg-gray-50' : '' }}">
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
                                                   <span
                                                       class="ml-2 text-xs-medium text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-full">Vous</span>
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
                                        $roleClass = match($member->pivot->role) {
                                            'admin' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                            'editor' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'viewer' => 'bg-rose-50 text-rose-700 border-rose-100',
                                            default => 'bg-slate-50 text-slate-700 border-slate-100'
                                        };

                                        $roleLabel = $isCurrentUser
                                            ? 'Responsable'
                                            : match($member->pivot->role) {
                                                'admin' => 'Administrateur',
                                                'editor' => 'Éditeur',
                                                'viewer' => 'Lecteur',
                                                default => 'Membre'
                                            };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs-medium border {{ $roleClass }}">
                                       {{ $roleLabel }}
                                   </span>
                                </td>
                                <td>
                                    @php
                                        $relation =  $member->pivot->relation;

                                        // match relation
                                        $relation = match($relation) {
                                            'father' => 'Père',
                                            'mother' => 'Mère',
                                            'brother' => 'Frère',
                                            'sister' => 'Soeur',
                                            'son' => 'Fils',
                                            'daughter' => 'Fille',
                                            'colleague' => 'Collègue',
                                            'colocataire' => 'Colocataire',
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
                                            @if($member->id === $currentUser)
                                                @if($members->count() > 1)
                                                    <x-menu.item type="link" href="#">
                                                        <x-svg.switch class="w-4 h-4 group-hover:text-gray-900"/>
                                                        {{ __('Changer de rôle') }}
                                                    </x-menu.item>
                                                @endif
                                                <x-menu.item type="link" href="{{ route('settings.profile') }}">
                                                    <x-svg.edit class="w-4 h-4 group-hover:text-gray-900"/>
                                                    {{ __('Modifier mon profil') }}
                                                </x-menu.item>
                                            @else
                                                <x-menu.item type="link" href="#">
                                                    <x-svg.edit class="w-4 h-4 group-hover:text-gray-900"/>
                                                    {{ __('Modifier le profil') }}
                                                </x-menu.item>
                                                <x-menu.item type="link" href="#">
                                                    <x-svg.switch class="w-4 h-4 group-hover:text-gray-900"/>
                                                    {{ __('Changer de rôle') }}
                                                </x-menu.item>
                                                <x-menu.item class="group hover:text-red-500">
                                                    <x-svg.trash class="w-4 h-4 group-hover:text-red-500"/>
                                                    {{ __('Supprimer de la famille') }}
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
                        <div class="p-4">
                            {{ $members->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </section>
    @endif
</div>
