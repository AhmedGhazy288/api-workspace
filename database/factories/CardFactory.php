<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CardType;
use App\Models\Client;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "card_type_id" => fake()->numberBetween(1 , count(CardType::all()) ),
            "rfid"=> fake()->name(),
            "cost_per_hour"=> fake()->numberBetween(1 , 1000)
        ];
    }
}
