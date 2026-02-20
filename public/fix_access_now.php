<?php
/**
 * Quick fix for "Access denied to patient record" error
 */

require __DIR__ . '/vendor/autoload.php';

use App\Database\Database;

echo "🔧 Quick Access Fix\n";
echo "===================\n\n";

$username = $argv[1] ?? 'testuser';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Step 1: Get user
    echo "Step 1: Finding user '$username'...\n";
    $sql = "SELECT id, username FROM users WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "   ❌ User not found! Creating user...\n";
        $hash = password_hash('password123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password, role) 
                VALUES (:username, :email, :password, 'user') 
                RETURNING id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':email' => $username . '@example.com',
            ':password' => $hash
        ]);
        $userId = $stmt->fetchColumn();
        echo "   ✅ User created (ID: $userId)\n";
    } else {
        $userId = $user['id'];
        echo "   ✅ User found (ID: $userId)\n";
    }
    
    // Step 2: Check for FY results and get patient_ids
    echo "\nStep 2: Checking for data...\n";
    $sql = "SELECT DISTINCT patient_id, COUNT(*) as count 
            FROM japanese_fy_results 
            GROUP BY patient_id 
            ORDER BY count DESC 
            LIMIT 1";
    $stmt = $conn->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $targetPatientId = $result['patient_id'];
        echo "   ✅ Found {$result['count']} FY results for patient_id: $targetPatientId\n";
    } else {
        echo "   ⚠️  No FY results found, using user_id as patient_id\n";
        $targetPatientId = $userId;
    }
    
    // Step 3: Ensure patient record exists
    echo "\nStep 3: Creating/verifying patient record...\n";
    $sql = "INSERT INTO patients (id, user_id, patient_code, full_name, is_active)
            VALUES (:id, :user_id, :code, :name, TRUE)
            ON CONFLICT (id) DO UPDATE 
            SET user_id = EXCLUDED.user_id, patient_code = EXCLUDED.patient_code
            RETURNING id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':id' => $targetPatientId,
        ':user_id' => $userId,
        ':code' => 'P' . str_pad($targetPatientId, 4, '0', STR_PAD_LEFT),
        ':name' => $username
    ]);
    $patientId = $stmt->fetchColumn();
    echo "   ✅ Patient record ready (ID: $patientId)\n";
    
    // Step 4: Create access mapping
    echo "\nStep 4: Granting access...\n";
    $sql = "INSERT INTO user_patient_mapping (user_id, patient_id, relationship, can_read, can_write)
            VALUES (:user_id, :patient_id, 'self', TRUE, TRUE)
            ON CONFLICT (user_id, patient_id) 
            DO UPDATE SET can_read = TRUE, can_write = TRUE";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':user_id' => $userId,
        ':patient_id' => $patientId
    ]);
    echo "   ✅ Access granted!\n";
    
    // Step 5: Verify
    echo "\nStep 5: Verification...\n";
    $sql = "SELECT COUNT(*) FROM japanese_fy_results WHERE patient_id = :patient_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':patient_id' => $patientId]);
    $count = $stmt->fetchColumn();
    
    echo "   Records accessible: $count\n";
    
    // Show summary
    echo "\n===================\n";
    echo "✅ FIX COMPLETE!\n";
    echo "===================\n";
    echo "Username: $username\n";
    echo "Password: password123\n";
    echo "Patient ID: $patientId\n";
    echo "Records: $count\n";
    echo "\n";
    
    if ($count == 0) {
        echo "⚠️  No records found. Options:\n";
        echo "1. Run: php database/seed_test_data.php\n";
        echo "2. Import existing data\n";
    } else {
        echo "✅ Ready to use!\n";
        echo "Login at: http://localhost:8000/login.php\n";
        echo "API: http://localhost:8000/api/japanese_fy_results.php\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
