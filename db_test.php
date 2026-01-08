<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=mystore', 'root', '');
    echo "Connected successfully to mystore with empty password.\n";
} catch (PDOException $e) {
    echo "Empty password failed: " . $e->getMessage() . "\n";
}

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=mystore', 'root', 'root');
    echo "Connected successfully to mystore with 'root' password.\n";
} catch (PDOException $e) {
    echo "'root' password failed: " . $e->getMessage() . "\n";
}

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    echo "Connected successfully to MySQL (no db, empty pass) \n";
} catch (PDOException $e) {
    echo "MySQL connection (no db) failed: " . $e->getMessage() . "\n";
}
