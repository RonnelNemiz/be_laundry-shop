<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;
use App\Models\Price;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query();
        $query->orderBy('id', 'desc');
        return ServiceResource::collection($this->paginated($query, $request));
       
    }

    public function store(Request $request)
    {

        $service = Service::where('name', $request->name)->first();


        if (empty($service)) {
             
            $newService = Service::create([
                
                "name" => $request->name,
                "description" => $request->description,
                "image" => $request->image,
            ]);
            Price::create([
                'price_id' =>  $newService->id,
                "price_value" => $request->price_value,
            ]);


            return response()->json([
                'status' => 200,
                'message' => "Sucessfully Added!"
            ]);
        };

        return response()->json([
            'status' => 500,
            'message' => "name is taken!"
        ]);
    }
   
    // public function edit(Request $id)
    //     {
    //         $service = Service::findOrFail($id);
    //         return view('services.edit', compact('service'));
    //     }
    public function update(Service $service, Request $request)
    {
        $service->update([
            
            'name' => $request->name,
            'description' => $request->description,
            'image' => $request->image,
        ]);

        $price = $service->price;

        $price->update([
            'price_value' => $request->price_value,
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Sucessfully Updated!"
        ]);
    }
   
        public function destroy(Service $service)
        {
            $service->price->delete();
            $service->delete();
            return response()->json([200, "Successfully Deleted!"]);
        }

}
