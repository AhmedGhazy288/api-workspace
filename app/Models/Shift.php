<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        "start_time" => "datetime",
        "end_time" => "datetime",
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function addToActive($reservationTotal, $productsTotal): bool
    {

        $activeUserShift = Shift::where("user_id", auth()->user()->id)
            ->where("end_time")
            ->latest()
            ->first();

        if (is_null($activeUserShift)) {
            return false;
        }

        $activeUserShift->reservations_total += $reservationTotal;
        $activeUserShift->products_total += $productsTotal;

        return $activeUserShift->save();
    }
}
