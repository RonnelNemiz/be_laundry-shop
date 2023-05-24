<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

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
}
