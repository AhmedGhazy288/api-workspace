<?php

namespace App\Util;

use Carbon\Carbon;

class Helpers
{

    public static function getThisMonth(string $column = 'created_at'): array
    {
        return [
            [$column, '>=', Carbon::now()->startOfMonth()],
            [$column, '<', Carbon::now()->endOfMonth()],
        ];
    }
}
