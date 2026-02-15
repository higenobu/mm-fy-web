<?php
header('Content-Type: application/json');

try {
    // Test autoload
    if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
        throw new Exception('Autoload file not found at: ' . __DIR__ . '/../../vendor/autoload.php');
    }
    
    require __DIR__ . '/../../vendor/autoload.php';
//  use App\Database\Database;  
    // Test Database class
    if (!class_exists('App\Database\Database')) {
        throw new Exception('Database class not found');
    }
    
//    use App\Database\Database;
    
    $db = new Database();
    $conn = $db->getConnection();
    
    // Test query
    $stmt = $conn->query("SELECT COUNT(*) as count FROM japanese_fy_results");
    $count = $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'message' => 'Database connection works',
        'record_count' => $count
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
