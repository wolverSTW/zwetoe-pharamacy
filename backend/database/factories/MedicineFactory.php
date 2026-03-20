<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medicine>
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => $this->faker->words(3, true),
            'generic_name' => $this->faker->word(),
            'sku_code' => $this->faker->unique()->bothify('MED-####-????'),
            'buy_price' => $this->faker->numberBetween(10, 100),
            'sell_price' => $this->faker->numberBetween(110, 200),
            'stock_quantity' => $this->faker->numberBetween(1, 100),
            'expiry_date' => $this->faker->dateTimeBetween('now', '+2 years'),
            'is_active' => true,
        ];
    }
}
