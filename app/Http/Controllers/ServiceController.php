<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;
use App\Models\Price;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return $services;
    }
   
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'service_name' => 'required|max:50',
            'description' => 'required|max:255',
            'image' => '',
        ]);

        $service = Service::create($validatedData);

        return response()->json([
            $service,
            'status' => 200,
        ]);
    }
    public function show()
    {
        $services = Service::get();
    
        return response()->json($services);
    }

    public function view($id)
    {
        $service = Service::find($id);

        if(!$service) {
            return response()->json([
                'message' => 'Service Not Found',
            ], 500);
        }
        return response()->json($service, 200);
    }

    public function update(Request $request, Service $service)
    {
        $validatedData = $request->validate([
            'service_name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'image' => '',
        ]);
    
        $service->update($validatedData);
    
        return response()->json([
            'message' => 'Handling data updated successfully',
            'data' => $service
        ], 200);
    }
    
    public function destroy(Service $service)
    {
        $service->delete();

        return response()->json([200, "Successfully Deleted!"]);
    }

 }

