<?php

namespace App\Http\Resources;

use App\Models\Fabcon;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Service;
use App\Models\Category;
use App\Models\Handling;
use App\Models\Detergent;
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

    private function groupedByParent($categories)
    {
        foreach ($categories as $category) {
            if ($category->parent_id == null) {
                $nestedCategories[$category->id] = [
                    'id' => $category->id,
                    'parent_id' => $category->parent_id,
                    'name' => $category->name,
                    'price' => $category->price,
                    'children' => [],
                ];
            } else {
                $nestedCategories[$category->parent_id]['children'] = [
                    'id' => $category->id,
                    'parent_id' => $category->parent_id,
                    'name' => $category->name
                ];
                $categoryChildren['children'] = [
                    'name' => $category->name
                ];
            }
        }
        return array_values($nestedCategories);
    }

    public function toArray($request)
    {
        
        return [
            'id' => $this->id,
            'trans_number' => $this->trans_number,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'total' => $this->total,
            'ref_num' => $this->ref_num,
            'change' => $this->change,
            'amount' => $this->amount,
            'approved_by' => $this->approved_by,
            'profile' => ProfileResource::collection(Profile::where('user_id', $this->user_id)->get()),
            'handling' => Handling::find($this->handling_id),
            'service' => Service::find($this->service_id),
            'fabcon' => Fabcon::find($this->fabcon_id),
            'detergent' => Detergent::find($this->detergent_id),
            'payment' => Payment::find($this->payment_id),
            'categories' => $this->categories,
            'created_at' => Carbon::parse($this->created_at)->format('m/d/Y'),
        ];
    }
}
