<?php

namespace Database\Factories;

use App\Models\InvoiceFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceFile>
 */
class InvoiceFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'file_path' => $this->faker->imageUrl(),
            'original_name' => $this->faker->sentence(),
            'mime_type' => $this->faker->mimeType(),
        ];
    }
}
