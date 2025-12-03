<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Product;
class ProductController extends Controller {
    public function index() {
        $products = Product::with('user')->latest()->paginate(20);
        return view('admin.products', compact('products'));
    }
}