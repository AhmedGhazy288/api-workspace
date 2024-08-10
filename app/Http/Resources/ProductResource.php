<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'scan_code' => $this->scan_code,
            'supplier' => [
                'id' => $this->supplier_id,
                'company' => $this->supplier->company,
            ],
            'name' => $this->name,
            'cost_price' => $this->cost_price,
            'retail_price' => $this->retail_price,
            'stock' => $this->stock,
            'photoUrl' => $this->photoUrl,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
