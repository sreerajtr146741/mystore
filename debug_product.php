<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = App\Models\Product::latest('updated_at')->first();
if ($p) {
    echo "ID: " . $p->id . "\n";
    echo "Name: " . $p->name . "\n";
    echo "Highlights Raw: " . json_encode($p->getRawOriginal('highlights')) . "\n";
    echo "Highlights Cast: " . json_encode($p->highlights) . "\n";
    echo "Specifications Raw: " . json_encode($p->getRawOriginal('specifications')) . "\n";
    echo "Specifications Cast: " . json_encode($p->specifications) . "\n";
} else {
    echo "No products found.\n";
}
