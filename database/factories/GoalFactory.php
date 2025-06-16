<?php

namespace Database\Factories;

use App\Enums\CategoryEnum;
use App\Livewire\Pages\Goals\GoalPeriodEnum;
use App\Livewire\Pages\Goals\GoalTypeEnum;
use App\Models\Goal;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoalFactory extends Factory
{
    protected $model = Goal::class;

    public function definition(): array
    {
        $startDate = now()->addDays($this->faker->numberBetween(0, 10));
        $endDate = $startDate->copy()->addDays($this->faker->numberBetween(10, 180));

        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional(0.75)->sentences(2, true),
            'goal_type' => $this->faker->randomElement(array_column(GoalTypeEnum::cases(), 'value')),
            'period_type' => $this->faker->randomElement(array_column(GoalPeriodEnum::cases(), 'value')),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_recurring' => $this->faker->boolean(20),
            'target_amount' => $this->faker->randomFloat(2, 10, 5000),
            'categories' => $this->faker->randomElements(
                array_column(CategoryEnum::cases(), 'value'),
                $this->faker->numberBetween(1, 3)
            ),
            'is_active' => true,
            'is_completed' => false,
            'current_amount' => 0,
            'completion_percentage' => 0,
        ];
    }
}
