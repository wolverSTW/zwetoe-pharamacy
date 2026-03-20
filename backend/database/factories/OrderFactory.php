<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'total_amount' => $this->faker->numberBetween(100, 5000),
            'status' => $this->faker->randomElement(['pending', 'completed', 'processing', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['cash', 'bank_transfer', 'kpay']),
            'payment_status' => $this->faker->randomElement(['unpaid', 'paid', 'refunded']),
            'shipping_method' => $this->faker->randomElement(['standard', 'express']),
            'address' => [
                'region' => $this->faker->state(),
                'township' => $this->faker->city(),
                'street' => $this->faker->streetAddress(),
                'house_number' => $this->faker->buildingNumber(),
            ],
        ];
    }
}
