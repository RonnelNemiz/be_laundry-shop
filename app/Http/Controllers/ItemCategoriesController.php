<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use App\Models\Service;
use Illuminate\Http\Request;

class ItemCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $itemCategories = ItemCategory::all();
        $itemCategoriesWithServiceName = [];

        foreach ($itemCategories as $itemCategory) {
            $serviceName = Service::where('id', $itemCategory->service_id)->value('name');

            $itemCategoriesWithServiceName[] = [
                'id' => $itemCategory->id,
                'name' => $itemCategory->name,
                'description' => $itemCategory->description,
                'price' => $itemCategory->price,
                'timestamp' => $itemCategory->timestamp,
                'service_name' => $serviceName,
            ];
        }
        // dd($itemCategoriesWithServiceName);
        return response()->json($itemCategoriesWithServiceName);
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
