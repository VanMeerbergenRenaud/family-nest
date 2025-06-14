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
        'period' => 'all',
        'type' => 'all',
        'owner' => 'all',
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
        $goal = Goal::findOrFail($goalId);
        $this->authorize('update', $goal);

        $this->resetModalState();
        $this->selectedGoal = $goal;
        $this->showModal = true;
    }

    public function openDeleteModal(int $goalId): void
    {
        $goal = Goal::findOrFail($goalId);
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
        $this->filters = array_fill_keys(array_keys($this->filters), 'all');
        $this->resetPage();
    }

    protected function getFilteredQuery(): Builder
    {
        $user = auth()->user();
        $familyId = $user->family()->first()->id;

        $query = Goal::query();

        $ownerEnum = GoalOwnerEnum::tryFrom($this->filters['owner']) ?? GoalOwnerEnum::All;
        $ownerEnum->applyQuery($query, $user->id, $familyId);

        // Application des filtres
        collect([
            'type' => fn (string $value) => $query->where('goal_type', $value),
            'period' => fn (string $value) => $query->where('period_type', $value),
        ])->each(function ($callback, $filterName) {
            if ($this->filters[$filterName] !== 'all') {
                $callback($this->filters[$filterName]);
            }
        });

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
