<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Database\Database;

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "=== Creating patient_memos Table ===\n\n";
    
    // Create table
    $sql = "CREATE TABLE IF NOT EXISTS public.patient_memos (
        id SERIAL PRIMARY KEY,
        patient_id INTEGER NOT NULL,
        title VARCHAR(255),
        comment TEXT,
        sentiment VARCHAR(1000),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $conn->exec($sql);
    echo "✓ Table 'patient_memos' created successfully!\n\n";
    
    // Create indexes
    echo "Creating indexes...\n";
    
    $conn->exec("CREATE INDEX IF NOT EXISTS idx_patient_memos_patient 
                 ON public.patient_memos(patient_id)");
    echo "✓ Index on patient_id created\n";
    
    $conn->exec("CREATE INDEX IF NOT EXISTS idx_patient_memos_created 
                 ON public.patient_memos(created_at DESC)");
    echo "✓ Index on created_at created\n";
    
    echo "\n✓ Table 'patient_memos' is ready!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
