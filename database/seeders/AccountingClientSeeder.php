<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use Illuminate\Support\Facades\DB;


class AccountingClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {

        $Clients = Client::all() ;

        foreach ($Clients as $Client) {
            DB::table('accounting_clients')->insert([
                'client_id' => $Client->id,
                'reservations_count' => 50,
                'reservations_total' => 50,
                'products_total' => 50,
                'final_total' => 50,
            ]);
        }


    }
}
