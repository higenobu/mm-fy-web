<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

use App\Database\Database;

echo "<h2>Testing Login System</h2>";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "<h3>✓ Database connected</h3>";
    echo "Database: " . $conn->query('SELECT current_database()')->fetchColumn() . "<br><br>";
    
    // Check if users table exists
    $stmt = $conn->query("SELECT to_regclass('users')");
    $tableExists = $stmt->fetchColumn();
    
    if ($tableExists) {
        echo "<h3>✓ Users table exists</h3>";
        
        // List all users
        $stmt = $conn->query("SELECT id, username, email, role, is_active FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<strong>Users in database:</strong><br>";
        echo "<pre>" . print_r($users, true) . "</pre>";
        
        // Test password verification
        $stmt = $conn->prepare("SELECT password_hash FROM users WHERE username = 'admin'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            echo "<br><strong>Testing password for 'admin':</strong><br>";
            $hash = $result['password_hash'];
            echo "Stored hash: " . substr($hash, 0, 30) . "...<br>";
            
            $testPassword = 'admin123';
            $verified = password_verify($testPassword, $hash);
            
            if ($verified) {
                echo "✓ Password 'admin123' is CORRECT<br>";
            } else {
                echo "✗ Password 'admin123' is INCORRECT<br>";
                echo "Creating new hash...<br>";
                
                // Create new user with correct password
                $newHash = password_hash('admin123', PASSWORD_BCRYPT);
                $stmt = $conn->prepare("
                    UPDATE users 
                    SET password_hash = :hash 
                    WHERE username = 'admin'
                ");
                $stmt->execute([':hash' => $newHash]);
                echo "✓ Password updated! Try logging in again.<br>";
            }
        } else {
            echo "✗ Admin user not found<br>";
        }
        
    } else {
        echo "<h3 style='color: red;'>✗ Users table does NOT exist</h3>";
        echo "Run this SQL to create it:<br>";
        echo "<textarea style='width:100%; height:200px;'>";
        echo file_get_contents(__DIR__ . '/../scripts/create_auth_tables.sql');
        echo "</textarea>";
    }
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Error: " . $e->getMessage() . "</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

