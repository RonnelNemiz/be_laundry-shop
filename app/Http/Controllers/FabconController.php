<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Fabcon;
use Illuminate\Http\Request;

class FabconController extends Controller
{
    public function index()
    {
        $fabcons = Fabcon::all();

        foreach ($fabcons as $fabcon) {
            $fabcon->image_url = asset('images/' . $fabcon->image);
        }

        return $fabcons;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fabcon_name' => 'required|max:50',
            'fabcon_price' => 'required|numeric',
            'fabcon_scoop' => 'required|numeric',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $validatedData['image'] = 'images/' . $imageName;
        }

        $fabcon = Fabcon::create($validatedData);

        return response()->json([
            $fabcon,
            'status' => 200,
        ]);
    }

    public function show()
    {
        $fabcons = Fabcon::get();

        $fabcons->image_url = asset('images/' . $fabcons->image);
        return response()->json($fabcons);
    }


    public function view($id)
    {
        $fabcon = Fabcon::find($id);

        if (!$fabcon) {
            return response()->json([
                'message' => 'Fabcon not found',
            ], 500);
        }

        return response()->json($fabcon, 200);
    }

    public function choose(Request $request, Fabcon $fabcon, Order $order)
    {
        $order->update([
            'fabcon_id' => $fabcon->id
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Successfully added fabcon!'
        ]);
    }
    

    public function update(Request $request, Fabcon $fabcon)
    {
        $validatedData = $request->except('image');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('images', 'public');
            $validatedData['image'] = $imagePath;
        } else {
            unset($validatedData['image']);
        }
        $fabcon->update($validatedData);

        return response()->json([
            'message' => 'Fabcon data updated successfully',
            'data' => $fabcon
        ], 200);
    }

    public function destroy(Fabcon $fabcon)
    {
        $fabcon->delete();
        return response()->json([200, "Successfully Deleted!"]);
    }
}
