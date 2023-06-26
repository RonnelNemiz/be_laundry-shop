<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Order;
use App\Models\Consumable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ConsumablesResource;

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

    public function addConsumables(Request $request, Order $order)
    {
        $consumables = $request->consumables;
        // Extract the IDs from the array of consumable objects
        $consumableIds = array_column($consumables, 'id');

        // $orderDetails = DB::table('order_details')->where('order_id', $order->id)->first();

        // $totalExpenses = 0;

        // foreach ($consumables as $consumable) {
        //     $totalExpenses += $consumable['price'];
        // }

        // // $exBreakdown = $this->generateBreakdownHtml($$consumables);

        // $orderDetails->update([
        //     'expenses' => $totalExpenses,
        //     'total' => $order->total + $totalExpenses,
        //     // 'expenses_breakdown' => $exBreakdown
        // ]);

        $order->consumables()->sync($consumableIds);

        return response()->json([
            'status' => 200,
            'message' => "Successfully added new consumables to order"
        ]);
    }

    public function updateOrderConsumableQuantity(Request $request, Order $order, $consumableId)
    {
        $orderDetails = DB::table('order_details')->where('order_id', $order->id)->first();

        $quantity = $request->quantity;

        $totalExpenses = $order->find($consumableId) * $quantity;

        $orderDetails->update([
            'expenses' => $totalExpenses,
            'total' => $order->total + $totalExpenses,
        ]);

        // Update the quantity for the order_consumables pivot
        $order->consumables()->syncWithoutDetaching([$consumableId => ['quantity' => $quantity]]);

        return response()->json([
            'status' => 200,
            'message' => 'Quantity updated successfully.',
        ]);
    }

    private function generateBreakdownHtml($children)
    {
        $html = '';
        $hasNonEmptyValue = false;

        foreach ($children as $child) {
            $name = $child['name'];
            $price = $child['price'];
            if (!empty($quantity)) {
                $hasNonEmptyValue = true;
                $html .= '<li>' . $price . ' x ' . $name . '</li>';
            }
        }

        if ($hasNonEmptyValue) {
            $html = '<ul>' . $html . '</ul>';
        }

        return $html;
    }

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
