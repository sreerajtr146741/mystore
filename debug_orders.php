<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Checking Latest Order...\n";
    $order = \App\Models\Order::with('items.product')->latest()->first();
    
    if (!$order) {
        echo "No orders found in DB.\n";
    } else {
        echo "Order ID: " . $order->id . "\n";
        echo "User ID: " . $order->user_id . "\n";
        echo "Total: " . $order->total . "\n";
        
        $items = $order->items;
        echo "Items Count: " . $items->count() . "\n";
        
        if ($items->count() > 0) {
            foreach ($items as $item) {
                echo "- Item: " . ($item->product ? $item->product->name : 'Unknown Product') . " | Qty: " . $item->qty . "\n";
            }
        } else {
            echo "Result: NO ITEMS FOUND.\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
