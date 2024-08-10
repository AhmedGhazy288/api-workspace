<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardType extends Model
{
    protected $fillable = [
        "name",
        "cost_per_hour",
        'max_hours',
        'max_cost',
        "status",
    ];
    public const NORMAL_ID = 1;
    public const SUB_ID = 4;


    public function cards()
    {
        return $this->hasMany(Card::class, "card_type_id", "id");
    }


    public static function getNormalCardCost()
    {
        return self::find(self::NORMAL_ID)->cost_per_hour;
    }
}
