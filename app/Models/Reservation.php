<?php /** @noinspection PhpUnused */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id',
        'shift_id',
        'client_id',
        "starts_at",
        "ends_at",
        'discount',
        "reservation_total",
        "products_total",
        "total",
        "amount_paid",
        'promo_code_id',
        'promo',
    ];

    protected $casts = [
        'promo' => 'json',
        "starts_at" => "datetime",
        "ends_at" => "datetime",
    ];

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function relPromo(): BelongsTo {
        return $this->belongsTo(PromoCode::class, 'promo_code_id');
    }

    public function reservationProducts(): HasMany
    {
        return $this->hasMany(ReservationProduct::class, "reservation_id", "id");
    }

    public function diffEndToStart(): float|int
    {
        return $this->ends_at->diffAsCarbonInterval($this->starts_at)->total('hours');
    }
}
