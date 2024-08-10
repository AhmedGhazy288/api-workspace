<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{

    protected $fillable = [
        'card_id',
        "client_id",
        "amount_payed",
        "ends_at",
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, "card_id");
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, "client_id");
    }
}
