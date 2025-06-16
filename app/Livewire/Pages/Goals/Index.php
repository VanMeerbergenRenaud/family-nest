<?php

namespace App\Livewire\Pages\Goals;

use App\Models\Goal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

#[Title('Objectifs')]
class Index extends Component
{
    use AuthorizesRequests, WithPagination;

    public ?Goal $selectedGoal = null;

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    #[Url]
    public array $filters = [
        'owner' => 'family',
        'period' => 'all',
        'type' => 'all',
    ];

    public function mount()
    {
        $this->resetModalState();
    }

    #[On('refreshGoals')]
    #[On('deleteAGoal')]
    public function refreshIndex(): void
    {
        $this->resetModalState();
    }

    #[On('modalClosed')]
    public function handleModalClosed(): void
    {
        $this->resetModalState();
    }

    private function resetModalState(): void
    {
        $this->selectedGoal = null;
        $this->showModal = false;
        $this->showDeleteModal = false;
    }

    public function openCreateModal(): void
    {
        $this->authorize('create', Goal::class);
        $this->resetModalState();
        $this->showModal = true;
    }

    public function openEditModal(int $goalId): void
    {
        $goal = auth()->user()->goals()->findOrFail($goalId);
        $this->authorize('update', $goal);

        $this->resetModalState();
        $this->selectedGoal = $goal;
        $this->showModal = true;
    }

    public function openDeleteModal(int $goalId): void
    {
        $goal = auth()->user()->goals()->findOrFail($goalId);
        $this->authorize('delete', $goal);

        $this->selectedGoal = $goal;
        $this->showDeleteModal = true;
    }

    public function deleteGoal(): void
    {
        if (! $this->selectedGoal) {
            return;
        }

        $this->authorize('delete', $this->selectedGoal);
        $this->selectedGoal->delete();

        Toaster::success('Objectif supprimé avec succès !');
        $this->resetModalState();
        $this->dispatch('deleteAGoal');
    }

    public function applyFilter(string $filter, string $value): void
    {
        $this->filters[$filter] = $value;
        $this->resetPage();
    }

    public function setFilterType(string $type): void
    {
        $this->applyFilter('owner', $type);
    }

    public function resetFilters(): void
    {
        $defaults = [
            'owner' => 'family',
            'period' => 'all',
            'type' => 'all',
        ];

        $this->filters = $defaults;
        $this->resetPage();
    }

    protected function getFilteredQuery(): Builder
    {
        $user = auth()->user();
        $userFamilyIds = $user->families()->pluck('families.id')->toArray();
        $query = Goal::query();

        // Filtre par propriétaire (personnel/familial)
        if ($this->filters['owner'] === GoalOwnerEnum::Personal->value) {

            // Objectifs personnels uniquement
            $query->where('user_id', $user->id)
                  ->where('is_family_goal', false);
        }
        elseif ($this->filters['owner'] === GoalOwnerEnum::Family->value) {
            // Objectifs familiaux uniquement
            if (empty($userFamilyIds)) {
                return Goal::query()->whereRaw('1 = 0');
            }

            $query->whereIn('family_id', $userFamilyIds)
                  ->where('is_family_goal', true);
        }
        else {
            // Tous les objectifs (personnels + familiaux)
            $query->where(function($q) use ($user, $userFamilyIds) {
                $q->where('user_id', $user->id)
                  ->where('is_family_goal', false);

                if (!empty($userFamilyIds)) {
                    $q->orWhere(function($subQ) use ($userFamilyIds) {
                        $subQ->whereIn('family_id', $userFamilyIds)
                             ->where('is_family_goal', true);
                    });
                }
            });
        }

        // Filtres additionnels
        if ($this->filters['period'] !== 'all') {
            $query->where('period_type', $this->filters['period']);
        }

        if ($this->filters['type'] !== 'all') {
            $query->where('goal_type', $this->filters['type']);
        }

        return $query;
    }

    public function render()
    {
        return view('livewire.pages.goals.index', [
            'hasFamily' => auth()->user()->hasFamily(),
            'goals' => $this->getFilteredQuery()->latest()->paginate(9),
            'periods' => GoalPeriodEnum::forFilter(),
            'types' => GoalTypeEnum::forFilter(),
            'owners' => GoalOwnerEnum::forFilter(),
        ])->layout('layouts.app-sidebar');
    }
}
