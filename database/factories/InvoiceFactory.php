<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['unpaid', 'paid', 'late', 'partially_paid'];
        $priorities = ['high', 'medium', 'low'];
        $paidMethods = ['cash', 'card', 'mastercard'];
        $tags = ['internet', 'logement', 'eau', 'électricité', 'gaz', 'téléphone'];

        return [
            'name' => $this->faker->sentence(),
            'file_path' => $this->faker->imageUrl(),
            'issuer' => $this->faker->company(),
            'type' => $this->faker->randomElement(['Facture', 'Reçu', 'Avoir']),
            'category' => $this->faker->randomElement(['Logement', 'Utilities', 'Transport', 'Santé']),
            'website' => $this->faker->url(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'is_variable' => $this->faker->boolean(),
            'is_family_related' => $this->faker->boolean(),
            'issued_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'payment_reminder' => $this->faker->randomElement(['1 jour', '1 semaine', '2 semaines']),
            'payment_frequency' => $this->faker->randomElement(['Mensuel', 'Trimestriel', 'Annuel']),
            'status' => $this->faker->randomElement($statuses),
            'payment_method' => $this->faker->randomElement($paidMethods),
            'priority' => $this->faker->randomElement($priorities),
            'notes' => $this->faker->paragraph(),
            'tags' => $this->faker->randomElements($tags, rand(1,4)), // Sélectionne 1 à 3 tags aléatoires
        ];
    }
}
