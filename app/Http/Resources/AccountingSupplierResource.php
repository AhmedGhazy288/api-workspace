<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountingSupplierResource extends JsonResource
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
            'supplier' => [
                'id' => $this->supplier_id,
                'company' => $this->supplier->company,
            ],
            'amount_paid' => $this->amount_paid,
            'amount_dept' => $this->amount_dept,
            'balance' => $this->balance,
            'created_at' => $this->created_at,
        ];
    }
}
