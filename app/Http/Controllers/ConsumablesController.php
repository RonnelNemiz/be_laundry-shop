<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConsumablesResource;
use App\Models\Consumable;
use Illuminate\Http\Request;
use Exception;

class ConsumablesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $consumables = Consumable::all();

        return ConsumablesResource::collection($consumables);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'cost' => 'required|numeric'
        ]);

        $newConsumable = Consumable::create($validatedData);

        return response()->json([
            'status' => 200,
            'message' => 'Consumable has been added successfully!',
            'data' => $newConsumable
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $consumable = Consumable::find($id);

        if ($consumable) {
            return response()->json([
                'status' => 200,
                'data' => $consumable
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $consumable = Consumable::find($id);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'cost' => 'required|numeric'
        ]);
        $consumable->update($validatedData);

        if ($consumable) {
            return response()->json([
                'status' => 200,
                'message' => 'Consumable item updated!',
                'data' => $consumable
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = Consumable::find($id);

        if ($payment) {
            try {
                $payment->delete();

                return response()->json(['status' => 200, 'message' => "Consumable Item Deleted!"]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 400,
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            return response()->json([
                'status' => 404,
                'error' => "Consumable Item not found"
            ]);
        }
    }
}
