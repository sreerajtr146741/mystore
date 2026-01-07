<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Get or create admin user
        $admin = User::where('email', 'admin@store.com')->first();
        
        if (!$admin) {
            $admin = User::create([
                'name' => 'Administrator',
                'firstname' => 'Admin',
                'lastname' => 'User',
                'email' => 'admin@store.com',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'phoneno' => '0000000000',
                'address' => 'Store HQ',
            ]);
        }

        // Get or create categories
        $categories = [
            'Mobile Phones' => Category::firstOrCreate(['name' => 'Mobile Phones']),
            'Laptops' => Category::firstOrCreate(['name' => 'Laptops']),
            'Tablets' => Category::firstOrCreate(['name' => 'Tablets']),
            'Smart Watches' => Category::firstOrCreate(['name' => 'Smart Watches']),
            'Headphones' => Category::firstOrCreate(['name' => 'Headphones']),
        ];

        // Sample products with Placeholder Images
        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'Latest Apple iPhone with A17 Pro chip, titanium design, and advanced camera system.',
                'price' => 999.99,
                'category' => 'Mobile Phones',
                'stock' => 50,
                'is_active' => true,
                'image' => 'https://placehold.co/600x400/1a1a1a/gold?text=iPhone+15+Pro',
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'description' => 'Premium Android phone with S Pen, 200MP camera, and AI features.',
                'price' => 1199.99,
                'category' => 'Mobile Phones',
                'stock' => 45,
                'is_active' => true,
                'image' => 'https://placehold.co/600x400/222222/FFF?text=Galaxy+S24+Ultra',
            ],
            [
                'name' => 'MacBook Pro 16"',
                'description' => 'Powerful laptop with M3 Pro chip, stunning Liquid Retina XDR display.',
                'price' => 2499.99,
                'category' => 'Laptops',
                'stock' => 30,
                'is_active' => true,
                'image' => 'https://placehold.co/600x400/c7c7c7/000?text=MacBook+Pro+16',
            ],
            [
                'name' => 'Dell XPS 15',
                'description' => 'Premium Windows laptop with Intel Core i7, NVIDIA graphics, and InfinityEdge display.',
                'price' => 1899.99,
                'category' => 'Laptops',
                'stock' => 25,
                'is_active' => true,
                'image' => 'https://placehold.co/600x400/333/FFF?text=Dell+XPS+15',
            ],
            [
                'name' => 'iPad Pro 12.9"',
                'description' => 'Professional tablet with M2 chip, ProMotion display, and Apple Pencil support.',
                'price' => 1099.99,
                'category' => 'Tablets',
                'stock' => 40,
                'is_active' => true,
                'image' => 'https://placehold.co/600x400/DDD/000?text=iPad+Pro',
            ],
            [
                'name' => 'Samsung Galaxy Tab S9',
                'description' => 'Premium Android tablet with S Pen, AMOLED display, and DeX mode.',
                'price' => 799.99,
                'category' => 'Tablets',
                'stock' => 35,
                'is_active' => true,
                'image' => 'https://placehold.co/600x400/111/FFF?text=Galaxy+Tab+S9',
            ],
            [
                'name' => 'Apple Watch Series 9',
                'description' => 'Advanced smartwatch with health tracking, always-on display, and ECG.',
                'price' => 399.99,
                'category' => 'Smart Watches',
                'stock' => 60,
                'is_active' => true,
                'image' => 'https://placehold.co/600x400/red/white?text=Apple+Watch+S9',
            ],
            [
                'name' => 'Samsung Galaxy Watch 6',
                'description' => 'Feature-rich smartwatch with Wear OS, health monitoring, and long battery life.',
                'price' => 299.99,
                'category' => 'Smart Watches',
                'stock' => 55,
                'is_active' => true,
                'image' => 'https://placehold.co/600x400/333/white?text=Galaxy+Watch+6',
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'description' => 'Industry-leading noise canceling headphones with exceptional sound quality.',
                'price' => 399.99,
                'category' => 'Headphones',
                'stock' => 70,
                'is_active' => true,
                'image' => 'https://placehold.co/600x400/000/FFF?text=Sony+XM5',
            ],
            [
                'name' => 'AirPods Pro (2nd Gen)',
                'description' => 'Premium wireless earbuds with active noise cancellation and spatial audio.',
                'price' => 249.99,
                'category' => 'Headphones',
                'stock' => 100,
                'is_active' => true,
                'image' => 'https://placehold.co/600x400/FFF/000?text=AirPods+Pro',
            ],
        ];

        foreach ($products as $productData) {
            $categoryName = $productData['category'];
            unset($productData['category']);
            
            $category = $categories[$categoryName];
            
            Product::create([
                'user_id' => $admin->id,
                'category_id' => $category->id,
                'category' => $categoryName, // Fix: Populate required string column
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'is_active' => $productData['is_active'],
                'image' => $productData['image'], // Added image
            ]);
        }

        $this->command->info('Sample products created successfully!');
    }
}
