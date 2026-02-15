<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Database\Database;

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "=== Creating japanese_sentiment_results Table ===\n\n";
    
    // Create table
    $sql = "CREATE TABLE IF NOT EXISTS public.japanese_sentiment_results (
        id SERIAL PRIMARY KEY,
        patient_id INTEGER NOT NULL,
        memo_id INTEGER,
        text TEXT NOT NULL,
        joy_score DECIMAL(10,6),
        fear_score DECIMAL(10,6),
        surprise_score DECIMAL(10,6),
        trust_score DECIMAL(10,6),
        ambiguity_score DECIMAL(10,6),
        intent_score DECIMAL(10,6),
        economic_hope_score DECIMAL(10,6),
        scores_json JSONB,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_memo FOREIGN KEY (memo_id) 
            REFERENCES public.patient_memos(id) 
            ON DELETE CASCADE
    )";
    
    $conn->exec($sql);
    echo "✓ Table 'japanese_sentiment_results' created successfully!\n\n";
    
    // Create indexes
    echo "Creating indexes...\n";
    
    $conn->exec("CREATE INDEX IF NOT EXISTS idx_jp_sentiment_patient 
                 ON public.japanese_sentiment_results(patient_id)");
    echo "✓ Index on patient_id created\n";
    
    $conn->exec("CREATE INDEX IF NOT EXISTS idx_jp_sentiment_memo 
                 ON public.japanese_sentiment_results(memo_id)");
    echo "✓ Index on memo_id created\n";
    
    $conn->exec("CREATE INDEX IF NOT EXISTS idx_jp_sentiment_created 
                 ON public.japanese_sentiment_results(created_at DESC)");
    echo "✓ Index on created_at created\n";
    
    $conn->exec("CREATE INDEX IF NOT EXISTS idx_jp_sentiment_json 
                 ON public.japanese_sentiment_results USING GIN (scores_json)");
    echo "✓ GIN index on scores_json created\n";
    
    echo "\n✓ All done! Table is ready to use.\n";
    
    // Show table structure
    echo "\n=== Table Structure ===\n";
    $stmt = $conn->query("
        SELECT 
            column_name, 
            data_type, 
            character_maximum_length,
            is_nullable,
            column_default
        FROM information_schema.columns 
        WHERE table_name = 'japanese_sentiment_results'
        AND table_schema = 'public'
        ORDER BY ordinal_position
    ");
    
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        echo sprintf(
            "  %-25s %-20s %s\n",
            $col['column_name'],
            $col['data_type'],
            $col['is_nullable'] === 'YES' ? 'NULL' : 'NOT NULL'
        );
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    if (strpos($e->getMessage(), 'patient_memos') !== false) {
        echo "\nNote: Make sure the 'patient_memos' table exists first.\n";
        echo "The foreign key constraint requires it.\n";
    }
}
