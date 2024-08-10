<?php

namespace App\Models;

use App\Util\Helpers;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationProduct extends Model
{

    use HasFactory;

    protected $guarded = [];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public static function getMostBoughtProductThisMonth()
    {

        DB::statement("SET SQL_MODE=''"); //FOR GROUP BY
        $productIdsInMonth = self::where(Helpers::getThisMonth())
            ->groupBy('product_id')->pluck('product_id');

        $thisMonthBoughtProducts = collect();
        foreach ($productIdsInMonth as $productId) {
            $sum = self::whereProductId($productId)
                ->where(Helpers::getThisMonth())
                ->sum('quantity');

            $thisMonthBoughtProducts->push([
                'product_id' => $productId,
                'sum' => $sum,
            ]);
        }

        return $thisMonthBoughtProducts->sortByDesc('sum')->first() ?? [];
    }
}
