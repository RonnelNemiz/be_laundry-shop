<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = User::where('id', $this->user_id)->first();

        return [
            'user_id' => $this->user_id,
            'address' => $this->address,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'image' => $this->image,
            'purok' => $this->purok,
            'brgy' => $this->brgy,
            'municipality' => $this->municipality,
            'contact_number' => $this->contact_number,
            'land_mark' => $this->land_mark,
            'user' => $user,
        ];
    }
}
