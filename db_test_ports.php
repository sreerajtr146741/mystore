<?php
echo "Testing Port 336...\n";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=336;dbname=mystore', 'root', '');
    echo "SUCCESS: Connected to Port 336 with empty password.\n";
} catch (PDOException $e) {
    echo "FAIL: Port 336 failed: " . $e->getMessage() . "\n";
}

echo "Testing Port 3306...\n";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=mystore', 'root', '');
    echo "SUCCESS: Connected to Port 3306 with empty password.\n";
} catch (PDOException $e) {
    echo "FAIL: Port 3306 failed: " . $e->getMessage() . "\n";
}
