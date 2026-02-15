<?php
header('Content-Type: application/json');

try {
    $autoloadPath = __DIR__ . '/../../vendor/autoload.php';
    
    if (!file_exists($autoloadPath)) {
        echo json_encode(['error' => 'Autoload not found', 'path' => $autoloadPath]);
        exit;
    }
    
    require $autoloadPath;
    
    echo json_encode(['step' => 2, 'message' => 'Autoload works']);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
