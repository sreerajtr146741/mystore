<?php
// Emergency debug script to check Laravel environment
echo "<h1>Debug Information</h1>";

echo "<h2>PHP Version</h2>";
echo phpversion();

echo "<h2>Document Root</h2>";
echo $_SERVER['DOCUMENT_ROOT'] ?? 'Not set';

echo "<h2>Script Filename</h2>";
echo $_SERVER['SCRIPT_FILENAME'] ?? 'Not set';

echo "<h2>Current Directory</h2>";
echo getcwd();

echo "<h2>Files in Current Directory</h2>";
echo "<pre>";
print_r(scandir('.'));
echo "</pre>";

echo "<h2>Files in Parent Directory</h2>";
echo "<pre>";
print_r(scandir('..'));
echo "</pre>";

echo "<h2>Environment Variables</h2>";
echo "<pre>";
echo "APP_ENV: " . (getenv('APP_ENV') ?: 'Not set') . "\n";
echo "APP_DEBUG: " . (getenv('APP_DEBUG') ?: 'Not set') . "\n";
echo "APP_KEY: " . (getenv('APP_KEY') ? 'Set (hidden)' : 'Not set') . "\n";
echo "DB_CONNECTION: " . (getenv('DB_CONNECTION') ?: 'Not set') . "\n";
echo "DB_HOST: " . (getenv('DB_HOST') ?: 'Not set') . "\n";
echo "</pre>";

echo "<h2>Check if index.php exists</h2>";
if (file_exists('index.php')) {
    echo "✅ index.php exists<br>";
    echo "Size: " . filesize('index.php') . " bytes<br>";
} else {
    echo "❌ index.php NOT found<br>";
}

echo "<h2>Check if autoload.php exists</h2>";
if (file_exists('../vendor/autoload.php')) {
    echo "✅ vendor/autoload.php exists<br>";
} else {
    echo "❌ vendor/autoload.php NOT found<br>";
}

echo "<h2>Check if bootstrap/app.php exists</h2>";
if (file_exists('../bootstrap/app.php')) {
    echo "✅ bootstrap/app.php exists<br>";
} else {
    echo "❌ bootstrap/app.php NOT found<br>";
}

echo "<h2>Try to load Laravel</h2>";
try {
    require __DIR__.'/../vendor/autoload.php';
    echo "✅ Autoload successful<br>";
    
    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "✅ Bootstrap successful<br>";
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✅ Kernel created<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>PHP Extensions</h2>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";
