<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = App\Models\Product::latest('updated_at')->first();
if ($p) {
    echo "Updating product {$p->id}...\n";
    $p->update([
        'highlights' => ['Test Highlight 1', 'Test Highlight 2'], 
        'specifications' => ['General' => [['key'=>'Test Key', 'value'=>'Test Value']]]
    ]);
    
    $p2 = App\Models\Product::find($p->id);
    echo "Highlights: " . json_encode($p2->highlights) . "\n";
    echo "Specs: " . json_encode($p2->specifications) . "\n";
}
