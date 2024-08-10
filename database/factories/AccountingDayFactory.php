<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountingDay>
 */
class AccountingDayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "day"=>  fake()->dateTime() ,
            'reservations_count' => 50,
            'reservations_total' => 50,
            'products_total' => 50,
            'final_total' => 50,
        ];
    }
}
