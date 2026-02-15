<?php

require __DIR__ . '../vendor/autoload.php';

use App\Database\Database;

try {
    $connection = Database::getConnection();
    echo "Database connection successful!";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage();
}
