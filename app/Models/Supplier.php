<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'company',
        'balance',
        'phone',
        'photo',
        "status"
    ];

    public function accounting() {
        return $this->hasMany(AccountingSupplier::class, 'supplier_id');
    }


    public function getPhotoUrlAttribute()
    {
        return empty($this->photo) ? '' :
            Storage::disk("suppliers")->url($this->photo);
    }
}
