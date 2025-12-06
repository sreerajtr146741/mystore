<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller {
    public function __construct() {
        $this->middleware('auth'); // Require login
    }

    public function index() {
        // Cart logic (session or DB)
        return view('cart.index');
    }

    public function add(Request $request) {
        // Add to cart, require auth
        // Redirect to cart after
        return redirect()->route('cart.index');
    }
}