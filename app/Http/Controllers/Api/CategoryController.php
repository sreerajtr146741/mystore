<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return response()->json(['status' => true, 'data' => $categories]);
    }

    public function show($id)
    {
        $category = Category::with('products')->find($id);
        if (!$category) return response()->json(['status' => false, 'message' => 'Category not found'], 404);
        
        return response()->json(['status' => true, 'data' => $category]);
    }
}
