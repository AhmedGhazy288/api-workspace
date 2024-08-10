<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CardTypeResource extends JsonResource
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
            "id"=>$this->id,
            "name"=>$this->name,
            "cost_per_hour"=>$this->cost_per_hour,
            "max_hours"=>$this->max_hours,
            "max_cost"=>$this->max_cost,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
