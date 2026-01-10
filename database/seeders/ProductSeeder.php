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
                'password' => 'admin123',
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

        // 6 Real Products with Unsplash Images
        $products = [
            [
                'name' => 'MacBook Pro 16 M3',
                'description' => 'The ultimate pro laptop. Blazing fast M3 chip, stunning Liquid Retina XDR display, and all-day battery life.',
                'price' => 2499.00,
                'category' => 'Laptops',
                'stock' => 15,
                'is_active' => true,
                'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Sony Alpha A7 IV',
                'description' => 'Professional full-frame mirrorless camera. 33MP Exmor R sensor, 4K 60p video, and real-time Eye AF.',
                'price' => 2498.00,
                'category' => 'Mobile Phones', // Putting in valid category even if mismatch name, or create new? Let's use 'Mobile Phones' or existing. Actually I created 'Mobile Phones', 'Laptops' etc above. I should stick to those. 
                // Wait, 'Cameras' is not in my category list above.
                // I should add 'Cameras' category.
            ],
            [
                'name' => 'iPhone 15 Pro Max',
                'description' => 'Titanium design, A17 Pro chip, and our most advanced camera system yet. The best iPhone ever.',
                'price' => 1199.00,
                'category' => 'Mobile Phones',
                'stock' => 50,
                'is_active' => true,
                'image' => 'https://images.unsplash.com/photo-1696446701796-da61225697cc?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'description' => 'Industry-leading noise cancellation, crystal clear hands-free calling, and 30-hour battery life.',
                'price' => 348.00,
                'category' => 'Headphones',
                'stock' => 100,
                'is_active' => true,
                'image' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'Apple Watch Ultra 2',
                'description' => 'Rugged and capable. Brightest display ever. Precision GPS. 36 hours of battery life.',
                'price' => 799.00,
                'category' => 'Smart Watches',
                'stock' => 30,
                'is_active' => true,
                'image' => 'https://images.unsplash.com/photo-1695669352796-06e902df5953?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'name' => 'iPad Air 5',
                'description' => 'Supercharged by the Apple M1 chip. 10.9-inch Liquid Retina display. 12MP Ultra Wide front camera.',
                'price' => 599.00,
                'category' => 'Tablets',
                'stock' => 45,
                'is_active' => true,
                'image' => 'https://images.unsplash.com/photo-1611532736597-6c7e23465bf9?auto=format&fit=crop&q=80&w=800',
            ],
            // Replacing Camera with existing category item -> Maybe another Laptop?
            // Actually I can add 'Cameras' category on the fly.
        ];

        // Add 'Cameras' to seeded categories if loop needs it, OR just map Camera to 'Mobile Phones' for now? No that's bad.
        // I will change the Camera product to another Laptop or Phone to stay safe with existing categories map.
        // Let's change item 2 (Camera) to a Gaming Laptop.
        $products[1] = [
             'name' => 'ASUS ROG Zephyrus G14',
             'description' => 'Ultra-powerful gaming laptop with Ryzen 9, RTX 4060, and 14" ROG Nebula Display.',
             'price' => 1599.00,
             'category' => 'Laptops',
             'stock' => 10,
             'is_active' => true,
             'image' => 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&q=80&w=800',
        ];

        foreach ($products as $productData) {
            $categoryName = $productData['category'];
            unset($productData['category']);
            
            // Ensure category exists even if not in top list (safety)
            $category = Category::firstOrCreate(['name' => $categoryName]);
            
            Product::create([
                'user_id' => $admin->id,
                'category_id' => $category->id,
                'category' => $categoryName, 
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'is_active' => $productData['is_active'],
                'image' => $productData['image'],
            ]);
        }

        $this->command->info('6 Real Products seeded successfully!');
    }
}
