<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationProductResource extends JsonResource
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
            "reservation_id" => $this->reservation_id,
            "product" => [
                'id' => $this->product_id,
                'name' => $this->product->name,
            ],
            "quantity" => $this->quantity,
            "item_price" => $this->item_price,
            "total" => $this->total,
        ];

    }
}
