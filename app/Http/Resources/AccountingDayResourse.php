<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountingDayResourse extends JsonResource
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
            'day' => $this->day,
            'reservations_count' => $this->reservations_count,
            'reservations_total' => $this->reservations_total,
            'subscriptions_total' => $this->subscriptions_total,
            'subscriptions_count' => $this->subscriptions_count,
            'products_total' => $this->products_total,
            'final_total' => $this->final_total,
            'created_at' => $this->created_at,
        ];
    }
}
