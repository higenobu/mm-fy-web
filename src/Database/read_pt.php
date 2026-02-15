<?php
require __DIR__ . '/../../vendor/autoload.php';

use App\Database\Database;
//require __DIR__ . '/Database.php';
//require 'src/Database/Database.php';
try {
    // Create database connection
    $db = new Database();
    $conn = $db->getConnection();
    
    // Query to retrieve all patient_memos
    $sql = "SELECT * FROM japanese_fy_results ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    // Fetch all results
    $memos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Display results
    echo "=== Patient Memos ===\n\n";
    echo "Total records: " . count($memos) . "\n\n";
    
    if (count($memos) > 0) {
        foreach ($memos as $index => $memo) {
            echo "--- Memo #" . ($index + 1) . " ---\n";
            foreach ($memo as $key => $value) {
                echo "$key: $value\n";
            }
            echo "\n";
        }
    } else {
        echo "No patient memos found.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
