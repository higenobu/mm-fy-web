<?php
require __DIR__ . '/../../vendor/autoload.php';
//require 'src/Database/Database.php';
//require __DIR__ . '/Database.php';
use App\Database\Database;

try {
    $connection = Database::getConnection();
    echo "Database connection successful!";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage();
}
