<?php

namespace Database\Factories;

use App\Models\Family;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Family>
 */
class FamilyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $relationTypes = [
            'admin',
            'viewer',
            'editor',
        ];

        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'avatar' => 'https://i.pravatar.cc/150?u=' . $this->faker->uuid,
            'relation_type' => $this->faker->randomElement($relationTypes),
            'is_active' => true,
            'is_primary' => false,
        ];
    }

    /**
     * Configure le membre comme membre principal.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
            'relation_type' => 'self',
        ]);
    }

    /**
     * Configure le membre comme conjoint.
     */
    public function spouse(): static
    {
        return $this->state(fn (array $attributes) => [
            'relation_type' => 'spouse',
        ]);
    }

    /**
     * Configure le membre comme enfant.
     */
    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'relation_type' => 'child',
        ]);
    }
}
