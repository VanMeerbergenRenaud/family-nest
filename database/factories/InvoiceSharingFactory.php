<?php

namespace Database\Factories;

use App\Models\InvoiceSharing;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceSharingFactory extends Factory
{
    protected $model = InvoiceSharing::class;

    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 0, 100);
        $percentage = $this->faker->randomFloat(2, 0, 100);

        return [
            'share_amount' => $amount,
            'share_percentage' => $percentage,
        ];
    }
}
