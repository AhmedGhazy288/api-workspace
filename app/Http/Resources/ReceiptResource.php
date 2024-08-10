<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptResource extends JsonResource
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
            "user" => $this->shift->user_id ? [
                'id' => $this->shift->user_id,
                'name' => $this->shift->user->name,
            ] : [],
            "card" => $this->card_id ? [
                'id' => $this->card_id,
                'type' => $this->card->cardType->name,
                'cost_per_hour' => $this->card->cardType->cost_per_hour,
                'balance' => $this->card->balance,
            ] : [],
            "client" => $this->client_id ? [
                'id' => $this->client_id,
                'name' => $this->client->name,
                'phone' => $this->client->phone,
            ] : [],
            "starts_at" => $this->starts_at,
            "ends_at" => $this->ends_at,
            'total_hours' => isset($this->ends_at) ? $this->diffEndToStart() : '',
            "reservation_total" => $this->reservation_total,
            "products_total" => $this->products_total,
            "total" => $this->total,
            "discount" => $this->discount,
            "created_at" => $this->created_at,
        ];
    }
}
