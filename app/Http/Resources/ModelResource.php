<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'attributes' =>[
                'year' =>$this->year,
                'price' =>$this->price,
                'engine_type' => $this->engine_type,
                'transmission_type' => $this->transmission_type,
                'seat_type' => $this->seat_type,
                'seats_count' => $this->seats_count,
                'acceleration' => $this->acceleration,
                'image' =>$this->image ? asset($this->image) : null

            ],
            'relationship' => [
                'Model Names' => [
                    'model_name_id' => (string)$this->modelName->id,
                    'model_name' => $this->modelName->name,
                ],
                'Types' => [
                    'type_id' => (string)$this->modelName->type->id,
                    'type_name' => $this->modelName->type->name,
                ],
                'Brand' => [
                    'brand_id' => $this->modelName->type->brand->id,

                    'brand_name' => $this->modelName->type->brand->name,
                ],

            ]

        ];
    
    }
}
