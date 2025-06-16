<div>
    <x-header
        title="Objectifs financiers"
        description="Définissez et suivez vos objectifs financiers pour mieux gérer vos dépenses."
    />

    {{-- Filtres et bouton d'ajout --}}
    <div class="md:px-4 flex flex-col md:flex-row gap-3 justify-between w-full my-4">
        {{-- Filtre personnel/famille (style similaire aux archives) --}}
        @if($hasFamily)
            <div class="h-fit p-1 flex items-center gap-1 w-fit rounded-lg border border-slate-200">
                @foreach($owners as $value => $label)
                    <button
                        type="button"
                        wire:click="setFilterType('{{ $value }}')"
                        class="px-3 py-1 text-sm rounded-md {{ $filters['owner'] === $value ? 'bg-indigo-500 text-white' : 'hover:bg-gray-200/50' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        @endif

        <div class="flex flex-wrap md:justify-end gap-2 w-full">
            {{-- Dropdown pour la période --}}
            <x-menu>
                <x-menu.button class="min-w-44 button-primary justify-between">
                    {{ $periods[$filters['period']] ?? 'Toutes périodes' }}
                    <x-svg.chevron-down class="ml-1 text-gray-500" />
                </x-menu.button>

                <x-menu.items class="w-56">
                    @foreach($periods as $value => $label)
                        <x-menu.item
                            wire:click="applyFilter('period', '{{ $value }}')"
                            class="{{ $filters['period'] === $value ? 'bg-teal-100 text-teal-900 font-medium' : '' }}"
                        >
                            {{ $label }}
                        </x-menu.item>
                    @endforeach
                </x-menu.items>
            </x-menu>

           {{-- Dropdown pour le statut --}}
            {{--
                <x-menu>
                    <x-menu.button class="min-w-44 button-primary justify-between">
                        {{ $statuses[$filters['status']] ?? 'Tous statuts' }}
                        <x-svg.chevron-down class="ml-1 text-gray-500" />
                    </x-menu.button>

                    <x-menu.items class="w-56">
                        @foreach($statuses as $value => $label)
                            @php
                                // Compter le nombre d'objectifs par statut (à implémenter si nécessaire)
                                $count = $value === 'all' ? $goals->total() : '-';
                            @endphp
                            <x-menu.item
                                wire:click="applyFilter('status', '{{ $value }}')"
                                class="{{ $filters['status'] === $value ? 'bg-teal-100 text-teal-900 font-medium' : '' }} flex justify-between"
                            >
                                {{ $label }}
                                @if($value !== 'all')
                                    <span class="text-gray-500 text-xs">{{ $count }}</span>
                                @endif
                            </x-menu.item>
                        @endforeach
                    </x-menu.items>
                </x-menu>
            --}}

            {{-- Dropdown pour le type d'objectif --}}
            <x-menu>
                <x-menu.button class="min-w-44 button-primary justify-between">
                    {{ $types[$filters['type']] ?? 'Tous types' }}
                    <x-svg.chevron-down class="ml-1 text-gray-500" />
                </x-menu.button>

                <x-menu.items class="w-56">
                    @foreach($types as $value => $label)
                        <x-menu.item
                            wire:click="applyFilter('type', '{{ $value }}')"
                            class="{{ $filters['type'] === $value ? 'bg-teal-100 text-teal-900 font-medium' : '' }}"
                        >
                            {{ $label }}
                        </x-menu.item>
                    @endforeach
                </x-menu.items>
            </x-menu>

            {{-- Si les 3 filtres sont différents du statut 'all' alors on affiche le reset --}}
            @if($filters['period'] !== 'all' || $filters['type'] !== 'all')
                <button
                    type="button"
                    wire:click="resetFilters"
                    wire:loading.attr="disabled"
                    class="button-classic"
                >
                    <x-svg.reset class="mr-1" />
                    Réinitialiser
                </button>
            @endif

            {{-- Bouton d'ajout --}}
            <button
                type="button"
                class="button-tertiary"
                wire:click="openCreateModal"
                wire:loading.attr="disabled"
                wire:target="openCreateModal"
            >
                <x-svg.target class="text-white"/>
                {{ __('Se fixer un objectif') }}
            </button>
        </div>
    </div>

    {{-- État vide --}}
    @if($goals->isEmpty())
        <div class="md:px-4">
            <div class="flex flex-col items-center justify-center bg-gray-50/60 border border-slate-200/80 rounded-xl p-8">
                <div class="mb-6 p-2 bg-gray-50 rounded-full border border-slate-200">
                    <x-svg.target class="w-5 h-5 text-slate-400"/>
                </div>

                <h3 role="heading" aria-level="3" class="text-lg-medium text-slate-800 mb-2.5">
                    @if($filters['owner'] === \App\Livewire\Pages\Goals\GoalOwnerEnum::Personal->value)
                        Aucun objectif personnel trouvé
                    @else
                        Aucun objectif familial trouvé
                    @endif
                </h3>

                <p class="text-sm text-slate-500 text-center max-w-md mx-auto">
                    @if($filters['owner'] === \App\Livewire\Pages\Goals\GoalOwnerEnum::Personal->value)
                        Définissez vos premiers objectifs financiers et transformez vos habitudes&nbsp;! Chaque euro économisé aujourd'hui est un pas vers la liberté financière de demain.
                    @else
                        Créez des objectifs communs et renforcez votre dynamique familiale&nbsp;! Des vacances de rêve aux projets d'avenir, tout devient possible avec une vision partagée.
                    @endif
                </p>
            </div>
        </div>
    @else
        {{-- Liste des objectifs --}}
        <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:px-4">
            @foreach($goals as $goal)
                <li class="bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col h-full">
                    {{-- En-tête --}}
                    <div class="px-4 py-3 border-b bg-slate-50 border-slate-100">
                        <div class="flex flex-wrap items-center justify-between">
                            <div class="flex items-center gap-2 min-h-[2rem]">
                                <h3 class="text-md-semibold truncate max-w-xs">{{ $goal->name }}</h3>
                            </div>

                            @can('update', $goal)
                                <x-menu>
                                    <x-menu.button class="p-1 rounded-lg hover:bg-gray-100">
                                        <x-svg.dots class="w-5 h-5 text-gray-500"/>
                                    </x-menu.button>
                                    <x-menu.items>
                                        <x-menu.item wire:click="openEditModal({{ $goal->id }})">
                                            <x-svg.edit class="group-hover:text-gray-900"/>
                                            {{ __('Modifier') }}
                                        </x-menu.item>
                                        @can('delete', $goal)
                                            <x-menu.item wire:click="openDeleteModal({{ $goal->id }})" class="group hover:text-red-500">
                                                <x-svg.trash class="group-hover:text-red-500"/>
                                                {{ __('Supprimer') }}
                                            </x-menu.item>
                                        @endcan
                                    </x-menu.items>
                                </x-menu>
                            @endcan
                        </div>
                    </div>

                    {{-- Corps avec info et progression --}}
                    <div class="p-4 h-full flex flex-col justify-between">
                        <div>
                            {{-- Montant cible --}}
                            <div class="flex justify-between items-baseline">
                                @php
                                    $goalTypeEnum = App\Livewire\Pages\Goals\GoalTypeEnum::tryFrom($goal->goal_type);
                                @endphp
                                <p class="text-sm uppercase text-gray-700">{{ $goalTypeEnum ? $goalTypeEnum->label() : 'Type inconnu' }}</p>
                                <p class="text-lg-semibold text-gray-900">{{ number_format($goal->target_amount, 2, ',', ' ') }} €</p>
                            </div>

                            {{-- Description--}}
                            @if($goal->description)
                                <p class="mt-2.5 mb-4 text-sm text-gray-500" title="{{ $goal->description }}">
                                    {{ Str::limit($goal->description, 165) }}
                                </p>
                            @endif

                            {{-- Période --}}
                            <div class="flex justify-between mt-3">
                                <span class="flex items-center gap-1.5 text-sm text-gray-600">
                                    <x-svg.calendar class="text-gray-600" />
                                    {{ $goal->start_date->format('d/m/Y') }} - {{ $goal->end_date->format('d/m/Y') }}
                                </span>
                                @php
                                    $periodEnum = App\Livewire\Pages\Goals\GoalPeriodEnum::tryFrom($goal->period_type);
                                @endphp
                                <span class="text-xs-medium px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                    {{ $periodEnum ? $periodEnum->label() : 'Personnalisé' }}
                                </span>
                            </div>

                            {{-- Barre de progression --}}
                            <div class="my-6">
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm text-gray-700">{{ number_format($goal->current_amount, 2, ',', ' ') }} €</span>
                                    <span class="text-sm text-gray-700">{{ number_format($goal->completion_percentage) }}%</span>
                                </div>

                                @php
                                    // Déterminer la couleur de la barre de progression
                                    $progressColor = 'bg-green-500';
                                    if ($goal->goal_type === App\Livewire\Pages\Goals\GoalTypeEnum::NotExceed->value) {
                                        if ($goal->completion_percentage > 90) {
                                            $progressColor = 'bg-red-500';
                                        } elseif ($goal->completion_percentage > 75) {
                                            $progressColor = 'bg-amber-500';
                                        }
                                    } else {
                                        if ($goal->completion_percentage < 25) {
                                            $progressColor = 'bg-red-500';
                                        } elseif ($goal->completion_percentage < 50) {
                                            $progressColor = 'bg-amber-500';
                                        }
                                    }
                                @endphp

                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="{{ $progressColor }} h-2 rounded-full" style="width: {{ $goal->completion_percentage }}%"></div>
                                </div>
                            </div>

                            {{-- Tags et catégories --}}
                            <div class="flex flex-wrap gap-1">
                                @if(!empty($goal->categories))
                                    @foreach($goal->categories as $category)
                                        @php
                                            $categoryEnum = App\Enums\CategoryEnum::tryFrom($category);
                                            $emoji = $categoryEnum ? $categoryEnum->emoji() : '';
                                        @endphp
                                        <span class="px-2 py-1 text-xs bg-blue-50 text-blue-600 rounded-full">
                                       {{ $emoji }} {{ $category }}
                                   </span>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        {{-- Partage avec la famille --}}
                        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap justify-between items-center gap-2">
                            {{-- Type d'objectif --}}
                            @if($goal->is_family_goal)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-sm bg-rose-50 text-rose-500 rounded-full">
                                    <x-svg.user-group class="w-3.5 h-3.5 text-rose-500" />
                                    Objectif familial
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-sm bg-blue-50 text-purple-500 rounded-full">
                                    <x-svg.lock-closed class="w-3.5 h-3.5 text-purple-500" />
                                    Objectif privé
                                </span>
                            @endif
                            {{-- Créateur --}}
                            @if($goal->user_id)
                                <span class="inline-flex items-center gap-1 text-sm text-slate-500">
                                    <x-svg.user class="w-3.5 h-3.5 text-slate-400" />
                                    {{ $goal->owner?->name ?? 'Utilisateur inconnu' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        {{-- Pagination --}}
        <div class="mt-4 px-4">
            {{ $goals->links() }}
        </div>
    @endif

    {{-- Modale unifiée --}}
    @if($showModal)
        <livewire:pages.goals.modal
            :goalId="$selectedGoal?->id"
            :showModal="true"
            :key="'goal-modal-' . ($selectedGoal?->id ?? 'create') . '-' . now()->timestamp"
        />
    @endif

    {{-- Modal de suppression --}}
    <x-loader.spinner target="deleteGoal" />
    @if($showDeleteModal)
        <x-modal wire:model="showDeleteModal">
            <x-modal.panel>
                <form wire:submit.prevent="deleteGoal">
                    @csrf

                    <div class="p-6">
                        <h3 class="text-lg-semibold text-gray-900">Supprimer l'objectif</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Êtes-vous sûr de vouloir supprimer l'objectif
                            <span class="text-sm-medium text-gray-600">{{ $selectedGoal->name }}</span>
                            ? Cette action ne peut pas être annulée.
                        </p>
                    </div>
                    <x-modal.footer>
                        <x-modal.close>
                            <button type="button" class="button-secondary">
                                {{ __('Annuler') }}
                            </button>
                        </x-modal.close>
                        <button type="submit" class="button-danger">
                            <x-svg.trash class="text-white"/>
                            {{ __('Supprimer') }}
                        </button>
                    </x-modal.footer>
                </form>
            </x-modal.panel>
        </x-modal>
    @endif
</div>
