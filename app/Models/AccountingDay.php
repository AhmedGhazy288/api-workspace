<?php

namespace App\Models;

use App\Util\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AccountingDay extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function add($reservation): Model|AccountingDay|bool
    {
        $day = Carbon::now()->format('Y-m-d');

        $accountingDay = self::where("day", $day)->first();

        if ($accountingDay) {
            ++$accountingDay->reservations_count;
            $accountingDay->reservations_total += $reservation->reservation_total;
            $accountingDay->products_total += $reservation->products_total;
            $accountingDay->final_total += $reservation->total;
            return $accountingDay->save();
        }

        return self::create([
            "day" => $day,
            "reservations_count" => 1,
            "reservations_total" => $reservation->reservation_total,
            "products_total" => $reservation->products_total,
            "final_total" =>
            $reservation->reservation_total +
                $reservation->products_total,
        ]);
    }

    public static function addSubscription($total): Model|AccountingDay|bool
    {
        $day = Carbon::now()->format('Y-m-d');

        $accountingDay = self::where("day", $day)->first();

        if ($accountingDay) {
            ++$accountingDay->subscriptions_count;
            $accountingDay->subscriptions_total += $total;
            $accountingDay->final_total += $total;
            return $accountingDay->save();
        }

        return self::create([
            "day" => $day,
            "subscriptions_count" => 1,
            "subscriptions_total" => $total,
            "reservations_count" => 0,
            "reservations_total" => 0,
            "products_total" => 0,
            "final_total" => $total,
        ]);
    }

    public static function editSubscription($createdDay, $amount): Model|AccountingClient|bool
    {
        $day =  Carbon::create($createdDay)->format('Y-m-d');
        $accountingDay = self::where("day", $day)->first();

        if ($accountingDay) {
            $accountingDay->subscriptions_total += $amount;
            $accountingDay->final_total += $amount;
            return $accountingDay->save();
        }
    }

    public static function deleteSubscription($createdDay, $amount): Model|AccountingClient|bool
    {
        $day =  Carbon::create($createdDay)->format('Y-m-d');
        $accountingDay = self::where("day", $day)->first();

        if ($accountingDay) {
            --$accountingDay->subscriptions_count;
            $accountingDay->subscriptions_total -= $amount;
            $accountingDay->final_total -= $amount;
            return $accountingDay->save();
        }
    }

    public static function getTotalReservationsThisMonth(): mixed
    {
        return self::where(Helpers::getThisMonth('day'))->sum('reservations_total');
    }

    public static function getTotalProductsThisMonth(): mixed
    {
        return self::where(Helpers::getThisMonth('day'))->sum('products_total');
    }
}
