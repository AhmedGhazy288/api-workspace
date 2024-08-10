<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CardSimpleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $subscription = $this->subscription;
       

        return [
            'id' => $this->id,
            'name' => implode(' - ', [$this->cardType->name, $this->rfid]),
            'card_type' => [
                'id' => $this->card_type_id,
                'name' => $this->cardType->name,
                'cost_per_hour' => $this->cardType->cost_per_hour,
            ],
            'rfid' => $this->rfid,
            "subscription"=> $this->card_type_id === 4 ? new SubscriptionResource($subscription) : null,

        ];
    }
}
