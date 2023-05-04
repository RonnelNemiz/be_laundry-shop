<?php

namespace App\Http\Resources;

use App\Http\Resources\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceResource extends JsonResource
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
            'price_id' => $this->price_id,
            'price_value' => $this->price_value,
        ];
    }
}
