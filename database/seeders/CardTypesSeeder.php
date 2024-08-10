<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CardTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private array $cardTypes = [
        [
            'name' => "Normal",
            'cost_per_hour' => 10,
            'max_hours' => 4,
            'max_cost' => 35,
        ],
        [
            'name' => "Meeting Room",
            'cost_per_hour' => 55,
            'max_hours' => 0,
            'max_cost' => 0,
        ],
        [
            'name' => 'Large Room',
            'cost_per_hour' => 65,
            'max_hours' => 0,
            'max_cost' => 0,
        ],
        [
            'name' => "Subscription",
            'cost_per_hour' => 10,
            'max_hours' => 0,
            'max_cost' => 0,
        ],
        [
            'name' => "Management",
            'cost_per_hour' => 0,
            'max_hours' => 0,
            'max_cost' => 0,
        ],
        [
            'name' => "Bean Bags",
            'cost_per_hour' => 65,
            'max_hours' => 0,
            'max_cost' => 0,
        ]
    ];


    public function run(): void
    {
        foreach ($this->cardTypes as $cardType) {
            DB::table('card_types')->insert($cardType);
        }

    }
}
