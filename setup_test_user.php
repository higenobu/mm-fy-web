<?php
/**
 * Quick setup script to create test user
 * Usage: php setup_test_user.php
 */

require __DIR__ . '/vendor/autoload.php';

use App\Database\Database;

$username = 'testuser';
$password = 'password123';
$email = 'testuser@example.com';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "🔧 Setting up test user...\n\n";
    
    // 1. Create users table if not exists
    echo "1. Checking users table...\n";
    $conn->exec("
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            role VARCHAR(20) DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "   ✅ Users table ready\n\n";
    
    // 2. Create user_patient_mapping table
    echo "2. Checking user_patient_mapping table...\n";
    $conn->exec("
        CREATE TABLE IF NOT EXISTS user_patient_mapping (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL,
            patient_id INTEGER NOT NULL,
            relationship VARCHAR(50) DEFAULT 'self',
            can_read BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(user_id, patient_id)
        )
    ");
    echo "   ✅ Mapping table ready\n\n";
    
    // 3. Create test user
    echo "3. Creating test user...\n";
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Delete existing user if present
    $conn->exec("DELETE FROM user_patient_mapping WHERE user_id IN (SELECT id FROM users WHERE username = '$username')");
    $conn->exec("DELETE FROM users WHERE username = '$username'");
    
    $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'user') RETURNING id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => $hash
    ]);
    $userId = $stmt->fetchColumn();
    
    echo "   ✅ User created with ID: $userId\n";
    echo "   Username: $username\n";
    echo "   Password: $password\n\n";
    
    // 4. Create patient mapping (user can access their own patient_id)
    echo "4. Creating patient mapping...\n";
    $sql = "INSERT INTO user_patient_mapping (user_id, patient_id, relationship) VALUES (:user_id, :patient_id, 'self')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':user_id' => $userId,
        ':patient_id' => $userId
    ]);
    echo "   ✅ User mapped to patient_id: $userId\n\n";
    
    // 5. Verify setup
    echo "5. Verifying setup...\n";
    $sql = "SELECT u.id, u.username, u.role, upm.patient_id 
            FROM users u 
            LEFT JOIN user_patient_mapping upm ON u.id = upm.user_id 
            WHERE u.username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':username' => $username]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "   ✅ Setup verified:\n";
        echo "      User ID: {$result['id']}\n";
        echo "      Username: {$result['username']}\n";
        echo "      Role: {$result['role']}\n";
        echo "      Patient ID: {$result['patient_id']}\n\n";
        
        // Check if there are any records for this patient
        $sql = "SELECT COUNT(*) FROM japanese_fy_results WHERE patient_id = :patient_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':patient_id' => $result['patient_id']]);
        $recordCount = $stmt->fetchColumn();
        
        echo "6. Checking data...\n";
        echo "   Records for patient_id {$result['patient_id']}: $recordCount\n\n";
        
        if ($recordCount == 0) {
            echo "   ⚠️  WARNING: No records found for this patient ID\n";
            echo "   Creating mapping to patient_id 2 (if exists)...\n";
            
            // Check if patient_id 2 has data
            $sql = "SELECT COUNT(*) FROM japanese_fy_results WHERE patient_id = 2";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $count2 = $stmt->fetchColumn();
            
            if ($count2 > 0) {
                // Add mapping to patient_id 2
                $sql = "INSERT INTO user_patient_mapping (user_id, patient_id, relationship) 
                        VALUES (:user_id, 2, 'self') 
                        ON CONFLICT (user_id, patient_id) DO NOTHING";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':user_id' => $userId]);
                echo "   ✅ User can now access patient_id 2 ($count2 records)\n\n";
            }
        }
    }
    
    echo "✅ Setup complete!\n\n";
    echo "You can now login with:\n";
    echo "  Username: $username\n";
    echo "  Password: $password\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
