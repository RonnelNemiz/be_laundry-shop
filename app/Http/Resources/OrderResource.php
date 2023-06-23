<?php

namespace App\Http\Resources;

use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'trans_number' => $this->trans_number,
            'first_name' => $this->profile->first_name,
            'last_name' => $this->profile->last_name,
            'service' => $this->service->name,
            'handling' => $this->handling->name,
            'status' => $this->status == 0 ? "Pending" : "Confirmed",
            'payment_status' => $this->payment->status == 0 ? "Unpaid" : "Paid",
            'created_at' => Carbon::parse($this->created_at)->format('m/d/Y'),
        ];
    }
}
