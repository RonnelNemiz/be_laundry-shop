<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ItemType;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::all();

        return response()->json([
            'status' => 200,
            'data' =>  CategoryResource::collection($this->groupedCategories($categories)),
        ]);
    }

    public function groupedCategories($categories)
    {
        foreach ($categories as $category) {
            if ($category->parent_id == null) {
                $nestedCategories[$category->id] = [
                    'id' => $category->id,
                    'parent_id' => $category->parent_id,
                    'name' => $category->name,
                    'children' => []
                ];
            } else {
                $nestedCategories[$category->parent_id]['children'][] = [
                    'id' => $category->id,
                    'parent_id' => $category->parent_id,
                    'name' => $category->name,
                ];
                $categoryChildren['children'] = [
                    'name' => $category->name
                ];
            }
        }

        return array_values($nestedCategories);
    }

    public function getCategoryWithChild()
    {
        // $parentCategories = ItemCategory::whereNull('parent_id')->get();

        // $list = [];
        // foreach ($parentCategories as $parent) {
        //     $item = $parent->toArray();
        //     $child = ItemCategory::where('parent_id', $parent->id)->get();
        //     $item['sub_categories'] = $child;
        //     array_push($list, $item);
        // }
        // return $list;

        $data = [];

        $itemCategories = ItemCategory::with('itemTypes')->get();

        foreach ($itemCategories as $itemCategory) {
            $categoryData = [
                'category_name' => $itemCategory->name,
                'item_types' => $itemCategory->itemTypes->toArray(),
            ];

            $data['item_categories'][] = $categoryData;
        }

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $service = Service::where('name', $request->service)->first();

        $newCategory = ItemCategory::create([
            'service_id' => $service->id,
            'name' => $request->category,
        ]);


        return response()->json([
            'status' => 200,
            'message' => "Successfully added new category"
        ]);
    }

    public function addItemCategory(Request $request)
    {
        $category = ItemCategory::where('name', $request->category)->first();

        $newItemCategory = ItemType::create([
            'category_id' => $category->id,
            'name' => $request->type
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Successfully added new category item"
        ]);
    }
}
