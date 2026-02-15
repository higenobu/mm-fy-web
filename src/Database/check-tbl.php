<?php

//require __DIR__ . '/Database.php';
require __DIR__ . '/../../vendor/autoload.php';

use App\Database\Database;
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // List all tables in PostgreSQL
    $sql = "SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = 'public' 
            ORDER BY table_name";
    
    $stmt = $conn->query($sql);
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "=== Available Tables ===\n\n";
    
    if (count($tables) > 0) {
        foreach ($tables as $table) {
            echo "- $table\n";
        }
    } else {
        echo "No tables found in database.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
