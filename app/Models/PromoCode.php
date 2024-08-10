<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'percent',
        'ends_at',
    ];

    //FOREIGN
    public function reservation(): HasOne
    {
        return $this->hasOne(PromoCode::class, 'promo_code_id');
    }


    //HELPERS

    /**
     * @throws Exception
     */
    public static function generateUniqueCode(): string
    {
        $code = Str::random(6);

        while (self::whereCode($code)->count() > 0) {
            $code = Str::random(6);
        }

        return $code;
    }
}
