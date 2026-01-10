<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductManagementController extends Controller
{
    public function index()
    {
        return response()->json(['status' => true, 'data' => Product::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'category' => 'required',
            // ... other fields
        ]);
        
        $product = Product::create($request->all());
        return response()->json(['status' => true, 'message' => 'Product created', 'data' => $product]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['status' => false, 'message' => 'Not found'], 404);
        
        $product->update($request->all());
        return response()->json(['status' => true, 'message' => 'Product updated']);
    }

    public function destroy($id)
    {
        Product::destroy($id);
        return response()->json(['status' => true, 'message' => 'Product deleted']);
    }
}
