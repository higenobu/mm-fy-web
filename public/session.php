<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

use App\Utils\SessionManager;
use App\Middleware\AuthMiddleware;

try {
    SessionManager::start();
    
    if (!SessionManager::isLoggedIn()) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'authenticated' => false,
            'error' => 'Not authenticated'
        ]);
        exit;
    }
    
    $userId = SessionManager::getUserId();
    $username = SessionManager::getUsername();
    
    // Get accessible patient IDs
    $patientIds = AuthMiddleware::getUserPatientIds($userId);
    
    echo json_encode([
        'success' => true,
        'authenticated' => true,
        'user' => [
            'id' => $userId,
            'username' => $username,
            'role' => $_SESSION['role'] ?? 'user'
        ],
        'accessible_patients' => $patientIds,
        'session_expires_in' => isset($_SESSION['login_time']) 
            ? 1800 - (time() - $_SESSION['login_time']) 
            : 0
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
