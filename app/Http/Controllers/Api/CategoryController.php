<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * List all categories
     */
    public function index()
    {
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get();

        return ApiResponse::success(['categories' => $categories]);
    }

    /**
     * Get category details with products
     */
    public function show($id)
    {
        $category = Category::with(['products' => function($query) {
            $query->where('status', 'active')->latest();
        }])->find($id);

        if (!$category) {
            return ApiResponse::notFound('Category not found');
        }

        return ApiResponse::success(['category' => $category]);
    }
}
