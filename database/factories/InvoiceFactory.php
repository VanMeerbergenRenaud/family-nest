<?php

namespace Database\Factories;

use App\Enums\InvoiceCategoryEnum;
use App\Enums\InvoiceTypeEnum;
use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
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
        $users = User::all();
        $userId = $users->count() > 0 ? $users->random()->id : 1;

        $issuedDate = $this->faker->dateTimeBetween('-2 year', 'now');
        $paymentDueDate = $this->faker->dateTimeBetween($issuedDate, '+2 month');

        return [
            // Informations générales
            'name' => $this->faker->words(3, true),
            'reference' => $this->faker->bothify('INV-#####-???'),
            'type' => $this->faker->randomElement(InvoiceTypeEnum::cases())->value,
            'category' => $this->faker->randomElement(InvoiceCategoryEnum::cases())->value,
            'issuer_name' => $this->faker->company(),
            'issuer_website' => $this->faker->url(),

            // Détails financiers
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => $this->faker->randomElement(['EUR', 'USD', 'GBP']),
            'paid_by_user_id' => $this->faker->randomElement($users->pluck('id')->toArray()),

            // Dates
            'issued_date' => Carbon::instance($issuedDate)->format('Y-m-d'),
            'payment_due_date' => Carbon::instance($paymentDueDate)->format('Y-m-d'),
            'payment_reminder' => Carbon::instance($this->faker->dateTimeBetween($issuedDate, $paymentDueDate))->format('Y-m-d'),
            'payment_frequency' => $this->faker->randomElement(PaymentFrequencyEnum::cases())->value,

            // Statut de paiement
            'payment_status' => $this->faker->randomElement(PaymentStatusEnum::cases())->value,
            'payment_method' => $this->faker->randomElement(PaymentMethodEnum::cases())->value,
            'priority' => $this->faker->randomElement(PriorityEnum::cases())->value,

            // Notes et tags
            'notes' => $this->faker->paragraph(),
            'tags' => $this->faker->words($this->faker->numberBetween(1, 5)),

            // États
            'is_archived' => $this->faker->boolean(20),
            'is_favorite' => $this->faker->boolean(5),

            // Clés étrangères
            'user_id' => $userId,
            'family_id' => $this->faker->randomElement($users->pluck('family_id')->toArray()),
        ];
    }
}
