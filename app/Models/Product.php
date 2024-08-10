<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'scan_code',
        'name',
        'photo',
        'cost_price',
        'retail_price',
        'stock',
        'status',
    ];

    protected $appends = [
        'photoUrl'
    ];

    public static function getNameById(int $productId)
    {
        return self::find($productId)->name;
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function getPhotoUrlAttribute()
    {
        return empty($this->photo) ? '' :
            Storage::disk("products")->url($this->photo);
    }


    public function itemPrice($id)
    {
        return self::find($id)->retail_price;
    }
}
