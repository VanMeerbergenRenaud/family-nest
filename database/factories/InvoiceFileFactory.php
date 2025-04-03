<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $extension = $this->faker->randomElement(['jpg', 'jpeg', 'png', 'pdf', 'docx']);
        $filename = $this->faker->unique()->word().'.'.$extension;
        $filePath = 'invoices/'.$filename;

        $fileSize = $this->faker->numberBetween(100 * 1024, 5 * 1024 * 1024);

        $compressionStatus = $this->faker->randomElement(['null', 'pending', 'completed', 'failed']);
        $originalSize = $this->faker->numberBetween(100 * 1024, 5 * 1024 * 1024);
        $compressionRate = $this->faker->randomFloat(2, 0, 100);

        return [
            'file_name' => $filename,
            'file_path' => $filePath,
            'file_extension' => $extension,
            'file_size' => $fileSize,
            'is_primary' => $this->faker->boolean(),
            'compression_status' => $compressionStatus,
            'original_size' => $originalSize,
            'compression_rate' => $compressionRate,
        ];
    }
}
