<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id',
        'client_id',
        'start_time',
        'end_time',
        'promo_code_id',
        'promo',
    ];

    protected $casts = [
        'promo' => 'json',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }
}
