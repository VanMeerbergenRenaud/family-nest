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
    public $is_family_goal = false;

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
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'is_recurring' => 'boolean|nullable',
            'is_family_goal' => 'boolean|nullable',
            'target_amount' => 'required|numeric|min:0|max:999999999.99',
            'categories' => 'required|array|min:1|max:10',
            'categories.*' => 'string|in:'.implode(',', array_map(fn ($case) => $case->value, CategoryEnum::cases())),
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
        $this->is_family_goal = $goal->family_id;
        $this->target_amount = $goal->target_amount;
        $this->categories = $goal->categories;
    }

    public function save()
    {
        $this->validate();

        try {
            $user = auth()->user();
            $familyId = $this->is_family_goal && $user->hasFamily() ? $user->family()->id : null;

            $data = [
                'name' => $this->name,
                'description' => $this->description,
                'goal_type' => $this->goal_type,
                'period_type' => $this->period_type,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'is_recurring' => $this->is_recurring,
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
