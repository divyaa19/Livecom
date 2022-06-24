<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(Request $request): \Illuminate\Http\JsonResponse
    {
        $category = Category::select('category_id', 'name')
            ->groupBy('category_id', 'name')->get();

        return response()->json([
            'status' => 'success',
            'success' => true,
            'categories' => $category
        ], 200);
    }

    public function findCategoryId(Request $request, $category_id)
    {

        $category = Category::where('category_id', $category_id)->first();

        return response()->json([
            'status' => 'success',
            'success' => true,
            'categories' => $category
        ], 200);

    }
}
