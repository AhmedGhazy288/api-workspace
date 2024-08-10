<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,

            'card' => [
                'id' => $this->card_id,
                'rifd' => $this->card->rfid,
                'balance' => $this->card->balance,
                'cost_per_hour' => $this->card->cost_per_hour,
            ],
            'client' => [
                'id' => $this->client_id,
                'name' => $this->client->name,
                'phone' => $this->client->phone,
                'username' => $this->client->username,
                'name_phone' => implode(' - ', [$this->client->name, $this->client->phone]),

            ],
            "amount_payed" => $this->amount_payed,
            "ends_at" => $this->ends_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
