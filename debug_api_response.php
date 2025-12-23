<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Simulating API Response for User 17 (from debug script)...\n";
    
    // Simulate what the controller does
    $orders = \App\Models\Order::where('user_id', 17)
            ->with('items.product')
            ->latest()
            ->paginate(10);
            
    // Convert to array like ApiResponse would (indirectly via json_encode)
    $json = json_encode($orders);
    $array = json_decode($json, true);
    
    if (isset($array['data']) && count($array['data']) > 0) {
        $firstOrder = $array['data'][0];
        echo "Order ID: " . $firstOrder['id'] . "\n";
        echo "Items Key Exists: " . (array_key_exists('items', $firstOrder) ? 'Yes' : 'NO') . "\n";
        
        if (array_key_exists('items', $firstOrder)) {
             echo "Items Count in JSON: " . count($firstOrder['items']) . "\n";
             if (count($firstOrder['items']) > 0) {
                 print_r($firstOrder['items'][0]);
             }
        }
    } else {
        echo "No orders found in pagination data.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
