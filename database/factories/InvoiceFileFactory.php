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
        // Liste des extensions possibles
        $extension = $this->faker->randomElement(['pdf', 'docx', 'jpg', 'jpeg', 'png']);

        // Nom du fichier fictif
        $filename = 'invoice_'.$this->faker->unique()->numberBetween(1000, 9999).'.'.$extension;

        // Chemin du fichier dans le stockage
        $filePath = 'invoices/'.$filename;

        // Taille aléatoire du fichier en octets (entre 100Ko et 5Mo)
        $fileSize = $this->faker->numberBetween(100 * 1024, 5 * 1024 * 1024);

        return [
            'file_name' => $filename,
            'file_path' => $filePath,
            'file_extension' => $extension,
            'file_size' => $fileSize,
            'is_primary' => $this->faker->boolean(),
            'compression_status' => $this->faker->randomElement(['null', 'pending', 'completed', 'failed']),
            'original_size' => $this->faker->numberBetween(100 * 1024, 5 * 1024 * 1024),
            'compression_rate' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}
