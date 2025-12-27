<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get seller dashboard statistics
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $sellerProducts = Product::where('user_id', $user->id)->get();
        
        $stats = [
            'count'       => $sellerProducts->count(),
            'total_value' => $sellerProducts->sum('price'),
            'low_stock'   => $sellerProducts->where('stock', '<=', 5)->count(),
        ];

        return ApiResponse::success([
            'stats' => $stats,
            'products' => $sellerProducts
        ]);
    }
}
