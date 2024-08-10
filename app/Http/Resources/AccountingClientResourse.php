<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountingClientResourse extends JsonResource
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
            'client' => [
                'id' => $this->client_id,
                'name' => $this->client->name,
                'phone' => $this->client->phone,
            ],
            'reservations_count' => $this->reservations_count,
            'reservations_total' => $this->reservations_total,
            'products_total' => $this->products_total,
            'subscriptions_total' => $this->subscriptions_total,
            'final_total' => $this->final_total,
            'total_hours' => $this->total_hours,
            'created_at' => $this->created_at,
        ];
    }
}
