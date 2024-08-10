<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShiftResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable
    {
        return [
            "id" => $this->id,
            "user" => [
                'id' => $this->user_id,
                'name' => $this->user->name,
            ],
            "start_time" => $this->start_time,
            "end_time" => $this->end_time,
            'reservations_total' => $this->reservations_total,
            'products_total' => $this->products_total,
            'total_sum' => $this->reservations_total + $this->products_total,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
