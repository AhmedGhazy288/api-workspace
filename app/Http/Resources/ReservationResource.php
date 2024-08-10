<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->card ? [
                'id' => $this->card->cardType->id,
                'cost_per_hour' => $this->card->cardType->cost_per_hour,
                'max_hours' => $this->card->cardType->max_hours,
                'max_cost' => $this->card->cardType->max_cost,
            ] : [],
            "card" => $this->card ? [
                'id' => $this->card_id,
                'type_id' => $this->card->cardType->id,
                'type' => $this->card->cardType->name,
                'rfid' => $this->card->rfid,
                'cost_per_hour' => $this->card->cost_per_hour,
                'balance' => $this->card->balance,
                'ends_at' => $this->ends_at ?? '',
            ] : [],
            "client" => $this->client ? [
                'id' => $this->client_id,
                'name' => $this->client->name,
                'phone' => $this->client->phone,
            ] : [],
            'has_promo' => isset($this->promo),
            'promo' => isset($this->promo) ? PromoCodeResource::make($this->promo) :  [],
            "starts_at" => $this->starts_at,
            "ends_at" => $this->ends_at,
            "reservation_total" => $this->reservation_total,
            "products_total" => $this->products_total,
            "discount" => $this->discount,
            "total" => $this->total,
        ];

    }
}
