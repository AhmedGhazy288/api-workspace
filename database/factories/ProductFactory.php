<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supplier;

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
    public function definition()
    {
        return [
            'supplier_id' => fake()->numberBetween(1, count(Supplier::all())),
            'scan_code' => fake()->unique()->numberBetween(1, 99999),
            'name' => fake()->name(),
            'photo' => "image.png",
            'cost_price' => fake()->numberBetween(1, 1000),
            'retail_price' => fake()->numberBetween(1, 1000),
            'stock' => fake()->numberBetween(1, 1000),
        ];

    }
}
