<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PendingreservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            "card" => $this->card_id ? [
                'id' => $this->card_id,
                'type' => $this->card->cardType->name,
                'cost_per_hour' => $this->card->cardType->cost_per_hour,
                'balance' => $this->card->balance,
                'rfid' => $this->card->rfid,
            ] : [],
            'client' => [
                'id' => $this->client_id,
                'name' => $this->client->name,
                'phone' => $this->client->phone,
            ],
            'has_promo' => isset($this->promo),
            'promo' => isset($this->promo) ? PromoCodeResource::make($this->promo) :  [],
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ];
    }
}
