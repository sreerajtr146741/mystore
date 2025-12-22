<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * List all products with filters and sorting
     */
    public function index(Request $request)
    {
        $query = Product::where('status', 'active');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['name', 'price', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 50); // Max 50 per page
        $products = $query->with('category')->paginate($perPage);

        return ApiResponse::success($products);
    }

    /**
     * Get product details
     */
    public function show($id)
    {
        $product = Product::with(['category', 'banners'])->find($id);

        if (!$product) {
            return ApiResponse::notFound('Product not found');
        }

        if ($product->status !== 'active') {
            return ApiResponse::notFound('Product not available');
        }

        return ApiResponse::success(['product' => $product]);
    }
}
