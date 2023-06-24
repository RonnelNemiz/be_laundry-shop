<?php

namespace App\Http\Resources;

use App\Models\Profile;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $profile = Profile::where('user_id', $this->id)->get();
        return [
            'id' => $this->id,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'profile' => ProfileResource::collection($profile)
        ];
    }
}
