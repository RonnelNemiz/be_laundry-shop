<?php

namespace App\Http\Controllers;

use App\Models\Handling;
use Illuminate\Http\Request;

class HandlingController extends Controller
{
    public function index()
    {
        $handlings = Handling::all();
        return $handlings;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric',
        ]);

        $handling = Handling::create($validatedData);

        // return response()->json($handling, 200);

        return response()->json([
            $handling,
            'status' => 200,
        ]);
    }

    public function show()
    {
        $handlings = Handling::get();

        return response()->json($handlings);
    }


    public function view($id)
    {
        $handling = Handling::find($id);

        if (!$handling) {
            return response()->json([
                'message' => 'Handling not found',
            ], 500);
        }

        return response()->json($handling, 200);
    }




    public function update(Request $request, Handling $handling)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        $handling->update($validatedData);

        return response()->json([
            'message' => 'Handling data updated successfully',
            'data' => $handling
        ], 200);
    }

    public function destroy(Handling $handling)
    {
        $handling->delete();

        return response()->json([200, "Successfully Deleted!"]);
    }
}
