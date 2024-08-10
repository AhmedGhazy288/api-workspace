<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionCount = count(Permission::all());


        for ($x = 1; $x <= $permissionCount ; $x++) {

            \App\Models\UserPermission::create([
                "user_id" => 2 ,
                "permission_id" => $x,
            ]);

        }


    }
}
