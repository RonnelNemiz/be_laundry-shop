<?php

namespace App\Http\Resources;
use App\Http\Resources\ProfileResource;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'ratings' => $this->ratings,
            'comments' => $this->comments,
            'reply' => $this->reply,
            'reply_at' => $this->reply_at,
            'user' => [
                'id' => $this->user->id,
                'first_name' => $this->user->profile ? $this->user->profile->first_name : null,
                'last_name' => $this->user->profile ? $this->user->profile->last_name : null,
            ],
        ];
    }
}
