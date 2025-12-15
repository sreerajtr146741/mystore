<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Iterate all products
        $products = Product::whereNotNull('category')->where('category', '!=', '')->get();

        foreach ($products as $product) {
            // Find or Create Category by name
            $cat = Category::firstOrCreate(['name' => $product->category]);
            
            // Update Product with ID
            $product->category_id = $cat->id;
            $product->saveQuietly(); // avoid timestamp updates if desired, or just save()
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse action needed really
    }
};
