<?php

//require __DIR__ . '/Database.php';
require __DIR__ . '/../../vendor/autoload.php';

use App\Database\Database;

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Sample data
    $samples = [
        ['patient_id' => 1, 'memo' => 'Patient shows improvement in blood pressure'],
        ['patient_id' => 1, 'memo' => 'Prescribed medication: Lisinopril 10mg'],
        ['patient_id' => 2, 'memo' => 'Follow-up appointment scheduled for next week'],
        ['patient_id' => 3, 'memo' => 'Patient complained of headaches'],
    ];
    
    $sql = "INSERT INTO public.pt_memo (pt_id, memo) VALUES (:patient_id, :memo)";
    $stmt = $conn->prepare($sql);
    
    $count = 0;
    foreach ($samples as $sample) {
        $stmt->execute($sample);
        $count++;
    }
    
    echo "✓ Inserted $count sample memos successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}



/*******************
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get database info
    $stmt = $conn->query("SELECT current_database(), current_user");
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "=== Database Connection Info ===\n\n";
    echo "Database: " . $info['current_database'] . "\n";
    echo "User: " . $info['current_user'] . "\n";
    echo "Connection: SUCCESS\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
*/

