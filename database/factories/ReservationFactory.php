<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;
use App\Models\Card;
use App\Models\Shift;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        return [
            "client_id" => fake()->numberBetween(1 , count(Client::all()) ),
            "shift_id" => fake()->numberBetween(1 , count(Shift::all()) ),
            "card_id" => fake()->numberBetween(1 , count(card::all()) ),
            "starts_at"=> "2022-11-10 10:56:22",
        ];

    }
}
