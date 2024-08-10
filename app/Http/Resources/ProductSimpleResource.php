<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductSimpleResource extends JsonResource
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
            'name' => implode(' - ', [$this->name, $this->scan_code]) . " (" . $this->stock . ")",
            'stock' => $this->stock,
            'scan_code' => $this->scan_code,
        ];
    }
}
