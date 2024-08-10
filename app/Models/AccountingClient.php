<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingClient extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public static function add($reservation): Model|AccountingClient|bool
    {
        $accountingClient = self::where("client_id", $reservation->client_id)->first();

        if ($accountingClient) {
            ++$accountingClient->reservations_count;
            $accountingClient->reservations_total += $reservation->reservation_total;
            $accountingClient->products_total += $reservation->products_total;
            $accountingClient->final_total += $reservation->total;
            $accountingClient->total_hours += $reservation->diffEndToStart();
            return $accountingClient->save();
        }

        return self::create([
            "client_id" => $reservation->client_id,
            "reservations_count" => 1,
            "reservations_total" => $reservation->reservation_total,
            "products_total" => $reservation->products_total,
            "subscriptions_total" => 0,
            "final_total" => $reservation->reservation_total + $reservation->products_total,
            'total_hours' => $reservation->diffEndToStart(),
        ]);
    }

    public static function addSubscription($subscription): Model|AccountingClient|bool
    {
        $accountingClient = self::where("client_id", $subscription->client_id)->first();

        if ($accountingClient) {
            $accountingClient->subscriptions_total += $subscription->amount_payed;
            $accountingClient->final_total += $subscription->amount_payed;
            return $accountingClient->save();
        }

        return self::create([
            "client_id" => $subscription->client_id,
            "reservations_count" => 0,
            "reservations_total" => 0,
            "products_total" => 0,
            "subscriptions_total" => $subscription->amount_payed,
            "final_total" => $subscription->amount_payed,
            'total_hours' => 0,
        ]);
    }


    public static function editSubscription($id, $amount): Model|AccountingClient|bool
    {
        $accountingClient = self::where("client_id", $id)->first();

        if ($accountingClient) {
            $accountingClient->subscriptions_total += $amount;
            $accountingClient->final_total += $amount;
            return $accountingClient->save();
        }

        return self::create([
            "client_id" => $id,
            "reservations_count" => 0,
            "reservations_total" => 0,
            "products_total" => 0,
            "subscriptions_total" => abs($amount),
            "final_total" => abs($amount),
            'total_hours' => 0,
        ]);
    }

    public static function deleteSubscription($id, $amount): Model|AccountingClient|bool
    {
        $accountingClient = self::where("client_id", $id)->first();

        if ($accountingClient) {
            $accountingClient->subscriptions_total -= $amount;
            $accountingClient->final_total -= $amount;
            return $accountingClient->save();
        }

    }
}
