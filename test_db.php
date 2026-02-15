<?php

require 'src/Database/Database.php';

use src\Database\Database;

try {
    $connection = Database::getConnection();
    echo "Database connection successful!";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage();
}
