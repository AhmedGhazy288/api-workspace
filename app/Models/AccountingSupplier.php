<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingSupplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'amount_paid',
        'amount_dept',
        'balance',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
