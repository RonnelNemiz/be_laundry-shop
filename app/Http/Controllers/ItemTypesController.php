<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use App\Models\ItemType;
use Illuminate\Http\Request;

class ItemTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $itemTypes = ItemType::all();

        $itemTypesWithCategories = [];

        foreach ($itemTypes as $itemType) {
            $category = ItemCategory::where('id', $itemType->category_id)->value('name');

            $itemTypesWithCategories[] = [
                'id' => $itemType->id,
                'name' => $itemType->name,
                'description' => $itemType->description,
                'price' => $itemType->price,
                'timestamp' => $itemType->timestamp,
                'category_name' => $category,
            ];
        }

        return response()->json($itemTypesWithCategories);
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
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ItemType $itemType)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => '',
        ]);

        $itemType->update($validatedData);

        return response()->json([
            'message' => 'Item type data updated successfully',
            'data' => $itemType
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemType $itemType)
    {
        $itemType->delete();

        return response()->json([200, "Successfully Deleted!"]);
    }
}
