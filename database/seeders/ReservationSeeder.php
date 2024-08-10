<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\ReservationProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reservations = Reservation::all();

        foreach( $reservations as $reservation ){
            DB::table('reservations')->where('id', $reservation->id)->update([
                // 'ends_at' => Carbon::now(),
                'reservation_total' =>  $reservation_total = ReservationProduct::where('id', $reservation->id)->sum('total'),
                'products_total' => $products_total = 100,
                'total' => $reservation_total + $products_total ,
            ]);
        }
    }
}
