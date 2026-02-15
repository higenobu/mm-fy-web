<?php
header('Content-Type: application/json');

try {
    require __DIR__ . '/../../vendor/autoload.php';
    
    if (!class_exists('App\Database\Database')) {
        echo json_encode(['error' => 'Database class not found']);
        exit;
    }
    
    echo json_encode(['step' => 3, 'message' => 'Database class found']);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
