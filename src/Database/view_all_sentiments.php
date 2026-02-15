<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Database\Database;

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "=== All Sentiment Results ===\n\n";
    
    $sql = "SELECT 
                js.id,
                js.memo_id,
                js.patient_id,
                pm.title,
                substr(pm.comment, 1, 40) as comment_preview,
                js.A_score,
                js.B_score,
                js.C_score,
                js.created_at
            FROM japanese_fy_results js
            LEFT JOIN patient_memos pm ON js.memo_id = pm.id
            ORDER BY js.created_at DESC";
    
    $stmt = $conn->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total: " . count($results) . " records\n\n";
    
    foreach ($results as $i => $row) {
        echo "┌─ #" . ($i + 1) . " ─────────────────────────────\n";
        echo "│ Sentiment ID: {$row['id']}\n";
        echo "│ Memo ID: {$row['memo_id']}\n";
        echo "│ Patient: {$row['patient_id']}\n";
        echo "│ Title: {$row['title']}\n";
        echo "│ Comment: {$row['comment_preview']}...\n";
        echo "│\n";
        echo "│ A:     " . number_format($row['A_score'], 2) . "\n";
        echo "│ B: " . number_format($row['B_score'], 2) . "\n";
        echo "│ C:       " . number_format($row['C_score'], 2) . "\n";
        echo "│\n";
        echo "│ Created: {$row['created_at']}\n";
        echo "└" . str_repeat("─", 50) . "\n\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
