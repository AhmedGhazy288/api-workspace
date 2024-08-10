<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PermissionSeeder extends Seeder
{


    public $permissions = ["users", "suppliers", "products", "clients", "cards", "reservations", "reports", "shifts", 'promo'];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        foreach ($this->permissions as $permission) {
            DB::table('permissions')->insert([
                'name' => $permission,
            ]);
        }


    }


}
