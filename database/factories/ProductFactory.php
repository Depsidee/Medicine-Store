<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tradeName' => $this->faker->text(10),
            'generic_name_id' => $this->faker->numberBetween(1,10),
            'category_id' => $this->faker->numberBetween(1,10),
            'company_id' => $this->faker->numberBetween(1,10),
            'price' => $this->faker->numberBetween(10,100),
            'amount' => $this->faker->numberBetween(1,200),
            'expiringYear' => $this->faker->randomElement([2024, 2030, 2050]),
            'expiringMonth' => $this->faker->numberBetween(1, 12),
            'expiringDay'=>$this->faker->numberBetween(1,30),
            'Show'=>0
        ];
    }
}
