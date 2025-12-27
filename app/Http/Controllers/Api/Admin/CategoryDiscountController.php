<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class CategoryDiscountController extends Controller
{
    /**
     * Set discount for a category
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id', 
            'discount_percent' => 'required|numeric|min:0|max:100',
            'discount_starts_at' => 'nullable|date',
            'discount_expires_at' => 'nullable|date|after_or_equal:discount_starts_at',
        ]);

        $category = Category::find($request->category_id);

        // Treat "0" as "Remove/Inherit" -> NULL
        $val = ($request->discount_percent > 0) ? $request->discount_percent : null;
        
        $category->update([
            'discount_percent' => $val,
            'discount_starts_at' => $request->discount_starts_at,
            'discount_expires_at' => $request->discount_expires_at,
        ]);

        return ApiResponse::success(
            ['category' => $category],
            'Discount updated for category'
        );
    }
}
