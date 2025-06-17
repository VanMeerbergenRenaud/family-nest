<?php

namespace App\Livewire\Forms;

use App\Enums\CategoryEnum;
use App\Livewire\Pages\Goals\GoalPeriodEnum;
use App\Livewire\Pages\Goals\GoalTypeEnum;
use App\Models\Goal;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Masmerise\Toaster\Toaster;

class GoalForm extends Form
{
    public ?Goal $goal = null;

    #[Validate]
    public $name = '';

    #[Validate]
    public $description = '';

    #[Validate]
    public $goal_type = 'not_exceed';

    #[Validate]
    public $period_type = 'monthly';

    #[Validate]
    public $start_date;

    #[Validate]
    public $end_date;

    #[Validate]
    public $is_recurring = false;

    #[Validate]
    public $is_family_goal = true;

    #[Validate]
    public $target_amount;

    #[Validate]
    public $categories = [];

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'goal_type' => 'required|in:'.implode(',', array_map(fn ($type) => $type->value, GoalTypeEnum::cases())),
            'period_type' => 'required|in:'.implode(',', array_map(fn ($period) => $period->value, GoalPeriodEnum::cases())),
            'start_date' => 'required|date|after_or_equal:'.now()->startOfYear()->format('Y-m-d'),
            'end_date' => 'required|date|after:start_date',
            'is_recurring' => 'boolean|nullable',
            'is_family_goal' => 'boolean|nullable',
            'target_amount' => 'required|numeric|min:0|max:999999999.99',
            'categories' => 'required|array|min:1|max:10',
            'categories.*' => 'string|in:'.implode(',', array_map(fn ($case) => $case->value, CategoryEnum::cases())),
        ];
    }

    public function messages(): array
    {
        return [
            // Nom de l'objectif
            'name.required' => 'Le nom de l\'objectif est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',

            // Description
            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description ne peut pas dépasser 500 caractères.',

            // Type d'objectif
            'goal_type.required' => 'Le type d\'objectif est obligatoire.',
            'goal_type.in' => 'Le type d\'objectif sélectionné n\'est pas valide.',

            // Période
            'period_type.required' => 'La période est obligatoire.',
            'period_type.in' => 'La période sélectionnée n\'est pas valide.',

            // Dates
            'start_date.required' => 'La date de début est obligatoire.',
            'start_date.date' => 'La date de début n\'est pas au format valide.',
            'start_date.after_or_equal' => 'La date de début doit être égale ou postérieure au début de l\'année courante.',
            'end_date.required' => 'La date de fin est obligatoire.',
            'end_date.date' => 'La date de fin n\'est pas au format valide.',
            'end_date.after' => 'La date de fin doit être postérieure à la date de début.',

            // Options
            'is_recurring.boolean' => 'L\'option de récurrence doit être oui ou non.',
            'is_family_goal.boolean' => 'L\'option d\'objectif familial doit être oui ou non.',

            // Montant
            'target_amount.required' => 'Le montant cible est obligatoire.',
            'target_amount.numeric' => 'Le montant cible doit être un nombre.',
            'target_amount.min' => 'Le montant cible doit être supérieur ou égal à zéro.',
            'target_amount.max' => 'Le montant cible doit être inférieur à 999 999 999,99.',

            // Catégories
            'categories.required' => 'Vous devez sélectionner au moins une catégorie.',
            'categories.array' => 'Les catégories doivent former une liste.',
            'categories.min' => 'Vous devez sélectionner au moins une catégorie.',
            'categories.max' => 'Vous ne pouvez pas sélectionner plus de 10 catégories.',
            'categories.*.in' => 'Une des catégories sélectionnées n\'est pas valide.',
        ];
    }

    public function setFromGoal(Goal $goal): void
    {
        $this->goal = $goal;
        $this->name = $goal->name;
        $this->description = $goal->description;
        $this->goal_type = $goal->goal_type;
        $this->period_type = $goal->period_type;
        $this->start_date = $goal->start_date->format('Y-m-d');
        $this->end_date = $goal->end_date->format('Y-m-d');
        $this->is_recurring = $goal->is_recurring;
        $this->is_family_goal = $goal->is_family_goal;
        $this->target_amount = $goal->target_amount;
        $this->categories = $goal->categories;
    }

    public function save()
    {
        $this->validate();

        try {
            $user = auth()->user();
            $family = $user->family();

            // Vérifier si l'utilisateur a une famille
            if (! $family) {
                // Si c'est un objectif familial mais que l'utilisateur n'a pas de famille
                if ($this->is_family_goal) {
                    Toaster::error('Vous devez appartenir à une famille pour créer un objectif familial');

                    return null;
                }
                $familyId = null;
            } else {
                $familyId = $family->id;
            }

            $data = [
                'name' => $this->name,
                'description' => $this->description,
                'goal_type' => $this->goal_type,
                'period_type' => $this->period_type,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'is_recurring' => $this->is_recurring,
                'is_family_goal' => $this->is_family_goal,
                'target_amount' => $this->target_amount,
                'categories' => $this->categories,
                'user_id' => $user->id,
                'family_id' => $familyId,
                'is_active' => true,
            ];

            $goal = Goal::updateOrCreate(
                ['id' => $this->goal?->id],
                $data
            );

            return $goal;
        } catch (\Exception $e) {
            Toaster::error('L\'objectif n\'a pas sur être enregistré');
            \Log::error('L\'objectif n\'a pas sur être enregistré : '.$e->getMessage());
        }
    }
}
