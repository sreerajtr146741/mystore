<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Api\StoreProductRequest;
use App\Http\Controllers\Api\UpdateProductRequest;
use Illuminate\Http\Request;

class ProductManagementController extends Controller
{
    /**
     * List all products
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate($request->get('per_page', 15));

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

        return ApiResponse::success(['product' => $product]);
    }

    /**
     * Create new product
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_url'] = asset('storage/' . $path);
        }

        $product = Product::create($data);

        return ApiResponse::created(
            ['product' => $product],
            'Product created successfully'
        );
    }

    /**
     * Update product
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return ApiResponse::notFound('Product not found');
        }

        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_url'] = asset('storage/' . $path);
        }

        $product->update($data);

        return ApiResponse::success(
            ['product' => $product->fresh()],
            'Product updated successfully'
        );
    }

    /**
     * Toggle product status
     */
    public function toggleStatus($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return ApiResponse::notFound('Product not found');
        }

        $product->update([
            'status' => $product->status === 'active' ? 'inactive' : 'active'
        ]);

        return ApiResponse::success(
            ['product' => $product],
            'Product status updated successfully'
        );
    }

    /**
     * Delete product
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return ApiResponse::notFound('Product not found');
        }

        $product->delete();

        return ApiResponse::success(null, 'Product deleted successfully');
    }
}
