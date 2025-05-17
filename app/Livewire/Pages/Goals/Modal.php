<?php

namespace App\Livewire\Pages\Goals;

use App\Enums\TypeEnum;
use App\Livewire\Forms\GoalForm;
use App\Models\Goal;
use Carbon\Carbon;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Modal extends Component
{
    public GoalForm $form;

    public ?int $goalId = null;

    public $goal = null;

    public $showModal = false;

    public $isEditMode = false;

    public $categorySearch = '';

    public $categoryView = 'list';

    public function mount($goalId = null, $showModal = false)
    {
        $this->showModal = $showModal;

        if ($goalId) {
            $this->configureForEdit($goalId);
        } else {
            $this->configureForCreate();
        }
    }

    protected function configureForEdit($goalId): void
    {
        $this->isEditMode = true;
        $this->goalId = $goalId;
        $this->goal = Goal::findOrFail($goalId);
        $this->form->setFromGoal($this->goal);
    }

    protected function configureForCreate(): void
    {
        $this->form->start_date = now()->format('Y-m-d');
        $this->form->end_date = now()->addMonth()->format('Y-m-d');
    }

    public function hydrate(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function updatedShowModal($value): void
    {
        if (! $value) {
            $this->dispatch('refreshGoals');
        }
    }

    public function saveGoal(): void
    {
        $this->form->validate();

        try {
            $this->form->save();

            Toaster::success($this->isEditMode
                ? 'Objectif mis à jour avec succès !'
                : 'Objectif créé avec succès !');

            $this->showModal = false;
            $this->dispatch('refreshGoals');
            $this->redirectRoute('goals');
        } catch (\Exception $e) {
            \Log::error('Une erreur est survenue : '.$e->getMessage());
        }
    }

    public function toggleCategory($categoryValue): void
    {
        if (in_array($categoryValue, $this->form->categories)) {
            $this->form->categories = array_values(array_filter($this->form->categories, function ($value) use ($categoryValue) {
                return $value !== $categoryValue;
            }));
        } else {
            $this->form->categories[] = $categoryValue;
        }
    }

    public function removeCategory($categoryValue): void
    {
        $this->form->categories = array_values(array_filter($this->form->categories, function ($value) use ($categoryValue) {
            return $value !== $categoryValue;
        }));
    }

    public function filterByType($typeValue): void
    {
        $this->categorySearch = $typeValue;
    }

    // Catégories groupées par type et filtrées par recherche
    public function getCategoriesByTypeProperty(): array
    {
        $result = [];
        $search = $this->categorySearch;

        foreach (TypeEnum::cases() as $type) {
            $filteredCategories = [];

            // Filtrer par type ou par terme de recherche
            if (! empty($search) && $search === $type->value) {
                // Afficher toutes les catégories de ce type
                foreach ($type->categoryEnums() as $category) {
                    $filteredCategories[$category->value] = $category;
                }
            } else {
                // Filtrer les catégories qui correspondent à la recherche
                foreach ($type->categoryEnums() as $category) {
                    if (empty($search) || stripos($category->label(), $search) !== false) {
                        $filteredCategories[$category->value] = $category;
                    }
                }
            }

            // Ajouter le type seulement s'il a des catégories correspondantes
            if ($filteredCategories) {
                $result[$type->value] = [
                    'type' => $type,
                    'categories' => $filteredCategories,
                ];
            }
        }

        return $result;
    }

    public function toggleCategoryView(): void
    {
        $this->categoryView = $this->categoryView === 'list' ? 'grid' : 'list';
    }

    public function render()
    {
        $goalTypes = array_filter(GoalTypeEnum::cases(), fn ($type) => $type !== GoalTypeEnum::All);
        $periodTypes = array_filter(GoalPeriodEnum::cases(), fn ($period) => $period !== GoalPeriodEnum::All);
        $startDate = Carbon::parse($this->form->start_date)->format('d/m/Y');
        $endDate = Carbon::parse($this->form->end_date)->format('d/m/Y');

        return view('livewire.pages.goals.modal', compact('goalTypes', 'periodTypes', 'startDate', 'endDate'))
            ->layout('layouts.app');
    }
}
