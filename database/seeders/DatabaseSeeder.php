<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\AccountingDay;
use App\Models\Card;
use App\Models\Client;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\Reservation;
use App\Models\ReservationProduct;
use App\Models\Shift;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'admin',
            'username' => 'admin',
            'password' => Hash::make('adminadminn'),
            'photo' => 'JL3mRbBLVYuuyBZxWAgwQfzasRtCIMkNDMXkEaIS.png',
            'type' => '0'
        ]);

        User::create([
            'name' => 'user',
            'username' => 'user',
            'password' => Hash::make(124578963),
        ]);

        //FIRST SHIFT
        $admin->shifts()->create([
            'start_time' => Carbon::now(),
        ]);

        $this->call([
            PermissionSeeder::class,
            UserPermissionSeeder::class,
            CardTypesSeeder::class,
        ]);

        Supplier::factory(100)->create();
        Product::factory(100)->create();
        Client::factory(10)->create();
        Card::factory(10)->create();
        Shift::factory(10)->create();
        Reservation::factory(100)->create();
        ReservationProduct::factory(100)->create();
        AccountingDay::factory(100)->create();
        $this->call([
            ReservationSeeder::class,
            AccountingClientSeeder::class,
        ]);
        PromoCode::factory(50)->create();
    }
}
