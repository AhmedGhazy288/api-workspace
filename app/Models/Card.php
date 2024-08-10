<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CardType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_type_id',
        "rfid",
        "balance",
        "cost_per_hour",
        "ends_at",
    ];

    public function cardType(): BelongsTo
    {
        return $this->belongsTo(CardType::class, "card_type_id");
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }
}
