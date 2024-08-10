<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoCodeResource extends JsonResource
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
            'id' => $this['id'] ?? '',
            'code' => $this['code'],
            'percent' => $this['percent'],
            'ends_at' => $this['ends_at'],
            'created_at' => $this['created_at'],
        ];

    }
}
