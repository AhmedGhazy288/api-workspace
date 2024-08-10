<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserPermission extends Pivot
{
    protected $table = 'user_permissions';
    use HasFactory;


    public $timestamps = false;
}
