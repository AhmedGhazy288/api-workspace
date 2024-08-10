<?php

namespace App\Models;

use App\Util\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingExpense extends Model
{
    protected $fillable = [
        'name',
        'cost',
    ];
    public static function getTotalThisMonth()
    {
        return self::where(Helpers::getThisMonth())->sum('cost');
    }

}
