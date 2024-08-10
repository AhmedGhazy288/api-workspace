<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'card_type' => [
                'id' => $this->card_type_id,
                'name' => $this->cardType->name,
                'cost_per_hour' => $this->cardType->cost_per_hour,
            ],
            'rfid' => $this->rfid,
            'cost_per_hour' => $this->cost_per_hour,
            'ends_at' => $this->ends_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }
}
