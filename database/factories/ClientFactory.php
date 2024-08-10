<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "name" => fake()->name(),
            "phone" => fake()->unique()->phoneNumber(),
            "username" => fake()->unique()->words(3, true),
            'password' => fake()->word(),
            'real_password' => fake()->word(),
        ];
    }
}
