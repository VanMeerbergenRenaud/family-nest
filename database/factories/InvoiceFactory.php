<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['abonnement', 'loyer', 'achat', 'service', 'assurance'];
        $categories = ['téléphonique', 'logement', 'internet', 'électricité', 'eau', 'transport', 'alimentation', 'santé'];
        $paymentMethods = ['card', 'cash', 'transfer', 'direct_debit', 'check'];
        $priorities = ['high', 'medium', 'low', 'none'];
        $paymentStatus = ['paid', 'unpaid', 'pending', 'late'];
        $paymentFrequencies = ['monthly', 'quarterly', 'annually', 'one_time'];

        $users = User::all();
        $userId = $users->count() > 0 ? $users->random()->id : 1;

        $issuedDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $paymentDueDate = $this->faker->dateTimeBetween($issuedDate, '+1 month');
        $paymentReminderDate = $this->faker->dateTimeBetween($issuedDate, $paymentDueDate);

        return [
            /* Étape 1 */
            'name' => $this->faker->words(3, true),
            'reference' => $this->faker->bothify('INV-#####-???'),
            'type' => $this->faker->randomElement($types),
            'category' => $this->faker->randomElement($categories),
            'issuer_name' => $this->faker->company(),
            'issuer_website' => $this->faker->url(),

            /* Étape 2 */
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => $this->faker->randomElement(['EUR', 'USD', 'GBP']),
            'paid_by_user_id' => $this->faker->randomElement($users->pluck('id')->toArray()),

            /* Étape 3 */
            'issued_date' => Carbon::instance($issuedDate)->format('Y-m-d'),
            'payment_due_date' => Carbon::instance($paymentDueDate)->format('Y-m-d'),
            'payment_reminder' => Carbon::instance($paymentReminderDate)->format('Y-m-d'),
            'payment_frequency' => $this->faker->randomElement($paymentFrequencies),

            /* Étape 4 */
            'payment_status' => $this->faker->randomElement($paymentStatus),
            'payment_method' => $this->faker->randomElement($paymentMethods),
            'priority' => $this->faker->randomElement($priorities),

            /* Étape 5 */
            'notes' => $this->faker->paragraph(),
            'tags' => $this->faker->words($this->faker->numberBetween(1, 5)),

            /* States */
            'is_archived' => $this->faker->boolean(20), // 20% chance d'être archivé
            'is_favorite' => $this->faker->boolean(5), // 5% chance d'être favori

            /* Foreign keys */
            'user_id' => $userId,
            'family_id' => $this->faker->randomElement($users->pluck('family_id')->toArray()),
        ];
    }

    /**
     * Indicate that the invoice is paid.
     */
    public function paid(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_status' => 'paid',
            ];
        });
    }

    /**
     * Indicate that the invoice is unpaid.
     */
    public function unpaid(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_status' => 'unpaid',
            ];
        });
    }

    /**
     * Indicate that the invoice is archived.
     */
    public function archived(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_archived' => true,
            ];
        });
    }

    /**
     * Indicate that the invoice is high priority.
     */
    public function highPriority(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 'high',
            ];
        });
    }

    /**
     * Set a specific invoice type.
     */
    public function ofType(string $type): self
    {
        return $this->state(function (array $attributes) use ($type) {
            return [
                'type' => $type,
            ];
        });
    }

    /**
     * Set a specific invoice category.
     */
    public function inCategory(string $category): self
    {
        return $this->state(function (array $attributes) use ($category) {
            return [
                'category' => $category,
            ];
        });
    }

    /**
     * Set a specific file size.
     */
    public function withFileSize(int $size): self
    {
        return $this->state(function (array $attributes) use ($size) {
            return [
                'file_size' => $size,
            ];
        });
    }
}
