<?php

namespace Database\Factories;

use App\Enums\CurrencyEnum;
use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Enums\TypeEnum;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(TypeEnum::cases())->value;
        $category = $this->faker->randomElement(TypeEnum::from($type)->categories());

        $issuedDate = $this->faker->dateTimeBetween('-2 year', 'now');
        $paymentDueDate = $this->faker->dateTimeBetween($issuedDate, '+2 month');

        return [
            // Informations générales
            'name' => $this->faker->words(3, true),
            'reference' => $this->faker->bothify('INV-#####'),
            'type' => $type,
            'category' => $category,
            'issuer_name' => $this->faker->company(),
            'issuer_website' => $this->faker->url(),

            // Détails financiers
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => $this->faker->randomElement(CurrencyEnum::cases())->value,
            'paid_by_user_id' => 1,

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
            'tags' => $this->faker->words($this->faker->numberBetween(5, 10)),

            // États
            'is_archived' => $this->faker->boolean(10),
            'is_favorite' => $this->faker->boolean(5),

            // Clés étrangères
            'user_id' => 1,
            'family_id' => 1,
        ];
    }
}
