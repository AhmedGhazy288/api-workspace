<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReservationProduct>
 */
class ReservationProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "reservation_id" => fake()->numberBetween(1 , count(Reservation::all()) ),
            "product_id" => $product_id = fake()->numberBetween(1 , count(Product::all()) ),
            "quantity"=> $quantity =fake()->numberBetween(1 , 3 ),
            "item_price"=> $item_price = (new Product)->itemPrice($product_id),
            "total"=> $item_price * $quantity,
        ];
    }
}
