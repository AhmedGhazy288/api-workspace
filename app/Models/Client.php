<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "phone",
        'username',
        'password',
        'real_password',
    ];

    public function activeReservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'client_id');
    }

    public function pendingReservations(): HasMany
    {
        return $this->hasMany(PendingReservation::class, 'client_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'client_id');
    }

    public static function createWithCredentials($data)
    {

        $prefix = 'u' . Carbon::now()->day . '_';
        $password = substr(uniqid($prefix, false), 0, 7);

        return self::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'real_password' => $data['real_password'],
            'username' => $data['phone'],
            'password' => $password,
        ]);
    }
}
