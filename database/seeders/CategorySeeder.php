<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing categories
        Category::truncate();

        // Parent Categories
        $vehicles = Category::create(['name' => 'Vehicles', 'parent_id' => null]);
        $electronics = Category::create(['name' => 'Electronics', 'parent_id' => null]);
        $clothing = Category::create(['name' => 'Clothing', 'parent_id' => null]);
        $home = Category::create(['name' => 'Home & Garden', 'parent_id' => null]);

        // Vehicle Subcategories
        Category::create(['name' => 'BMW', 'parent_id' => $vehicles->id]);
        Category::create(['name' => 'Toyota', 'parent_id' => $vehicles->id]);
        Category::create(['name' => 'Honda', 'parent_id' => $vehicles->id]);
        Category::create(['name' => 'Mercedes', 'parent_id' => $vehicles->id]);
        Category::create(['name' => 'Ford', 'parent_id' => $vehicles->id]);

        // Electronics Subcategories
        Category::create(['name' => 'Laptops', 'parent_id' => $electronics->id]);
        Category::create(['name' => 'Smartphones', 'parent_id' => $electronics->id]);
        Category::create(['name' => 'Tablets', 'parent_id' => $electronics->id]);
        Category::create(['name' => 'Cameras', 'parent_id' => $electronics->id]);
        Category::create(['name' => 'Headphones', 'parent_id' => $electronics->id]);

        // Clothing Subcategories
        Category::create(['name' => 'Men', 'parent_id' => $clothing->id]);
        Category::create(['name' => 'Women', 'parent_id' => $clothing->id]);
        Category::create(['name' => 'Kids', 'parent_id' => $clothing->id]);
        Category::create(['name' => 'Accessories', 'parent_id' => $clothing->id]);

        // Home & Garden Subcategories
        Category::create(['name' => 'Furniture', 'parent_id' => $home->id]);
        Category::create(['name' => 'Kitchen', 'parent_id' => $home->id]);
        Category::create(['name' => 'Decor', 'parent_id' => $home->id]);
        Category::create(['name' => 'Tools', 'parent_id' => $home->id]);
    }
}
