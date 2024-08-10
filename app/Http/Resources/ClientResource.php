<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "phone" => $this->phone,
            'credentials' => [
                'username' => $this->username,
                'password' => $this->password,
                'real_password' => $this->real_password,
            ],
            'name_phone' => implode(' - ', [$this->name, $this->phone]),
            'created_at' => $this->created_at,


        ];
    }
}
