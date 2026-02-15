<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\MemoController;

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $controller = new MemoController();
    
    $method = $_SERVER['REQUEST_METHOD'];
    $path = $_GET['path'] ?? '';
    
    switch ($method) {
        case 'POST':
            // Create new memo
            $controller->create();
            break;
            
        case 'GET':
            if (isset($_GET['id'])) {
                // Get single memo
                $controller->getMemo($_GET['id']);
            } elseif (isset($_GET['patient_id'])) {
                // Get memos for patient
                $controller->getMemos($_GET['patient_id']);
            } else {
                // Get all memos
                $controller->getMemos();
            }
            break;
            
        case 'PUT':
            // Update memo
            if (isset($_GET['id'])) {
                parse_str(file_get_contents('php://input'), $_POST);
                $controller->update($_GET['id']);
            } else {
                throw new Exception('Memo ID required for update');
            }
            break;
            
        case 'DELETE':
            // Delete memo
            if (isset($_GET['id'])) {
                $controller->delete($_GET['id']);
            } else {
                throw new Exception('Memo ID required for delete');
            }
            break;
            
        default:
            throw new Exception('Method not allowed');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
