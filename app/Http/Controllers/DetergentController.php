<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Detergent;
use Illuminate\Http\Request;

class DetergentController extends Controller
{
    public function index()
    {
        $detergents = Detergent::all();
        return $detergents;
    }
   
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'detergent_name' => 'required|max:50',
            'detergent_price' => 'required|numeric',
            'detergent_scoop' => 'required|numeric',
            'image' => '',
        ]);

        $detergent = Detergent::create($validatedData);

        // return response()->json($handling, 200);

        return response()->json([
            $detergent,
            'status' => 200,
        ]);
    }
    public function show()
    {
        $detergents = Detergent::get();
    
        return response()->json($detergents);
    }


    public function view($id)
    {
        $detergent = Detergent::find($id);

        if (!$detergent) {
            return response()->json([
                'message' => 'Detergent not found',
            ], 500);
        }

        return response()->json($detergent, 200);
    }
    public function update(Request $request, Detergent $detergent)
    {
        $validatedData = $request->validate([
            'detergent_name' => 'required|string|max:50',
            'detergent_price' => 'required|numeric',
            'detergent_scoop' => 'required|numeric',
            'image'=>'',
        ]);
    
        $detergent->update($validatedData);
    
        return response()->json([
            'message' => 'Detergent data updated successfully',
            'data' => $detergent
        ], 200);
    }

    
    public function chooseDetergent(Request $request, Detergent $detergent, Order $order)
    {
        $order->update([
            'detergent_id' => $detergent->id
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Successfully added Detergent!'
        ]);
    }

    public function destroy(Detergent $detergent)
    {
        $detergent->delete();

        return response()->json([200, "Successfully Deleted!"]);
    }
}
