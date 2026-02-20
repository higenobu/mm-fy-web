<?php
// Clean output buffer to prevent whitespace issues
if (ob_get_level()) ob_end_clean();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require __DIR__ . '/../../vendor/autoload.php';

use App\Database\Database;
use App\Utils\SessionManager;
use App\Middleware\AuthMiddleware;

try {
    // Start session and check authentication
    SessionManager::start();
    
    if (!SessionManager::isLoggedIn()) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Not authenticated',
            'authenticated' => false
        ]);
        exit;
    }
    
    $userId = SessionManager::getUserId();
    
    if (!$userId) {
        throw new Exception('User ID not found in session');
    }
    
    // Connect to database
    $db = new Database();
    $conn = $db->getConnection();
    
    // Simple rule: patient_id = user_id (no user_patient_mapping table)
    $patientId = $userId;
    
    // Get FY results for this user
    $sql = "SELECT id, patient_id, text, 
                   a_score, b_score, c_score, d_score, h_score,
                   i_score, j_score, k_score, l_score, m_score,
                   created_at
            FROM japanese_fy_results 
            WHERE patient_id = :patient_id
            ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':patient_id' => $patientId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response = [
        'success' => true,
        'user_id' => $userId,
        'patient_id' => $patientId,
        'count' => count($results),
        'data' => $results
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}

// Ensure no extra output
exit;
