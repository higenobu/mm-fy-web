<?php
ob_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate');

require __DIR__ . '/../../vendor/autoload.php';

use App\Database\Database;
use App\Utils\PersonalityTraits;
use App\Middleware\AuthMiddleware;

try {
    // ✅ SECURITY: Require authentication
    AuthMiddleware::requireAuth();
    
    // ✅ SECURITY: Get current user ID
    $currentUserId = AuthMiddleware::getUserId();
    
    if (!$currentUserId) {
        throw new Exception('User not authenticated');
    }
    
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get request parameters
    $requestedPatientId = $_GET['patient_id'] ?? null;
    $result_id = $_GET['id'] ?? null;
    
    if ($result_id) {
        // Get specific result
        // ✅ SECURITY: Check access to this specific record
        $sql = "SELECT jfr.* FROM japanese_fy_results jfr
                INNER JOIN user_patient_mapping upm 
                ON jfr.patient_id = upm.patient_id
                WHERE jfr.id = :id AND upm.user_id = :user_id";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $result_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $currentUserId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            http_response_code(403);
            throw new Exception('Record not found or access denied');
        }
        
        // Add personality profile
        $profile = PersonalityTraits::getPersonalityProfile($result);
        $bigFive = PersonalityTraits::getBigFiveSummary($result);
        
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'data' => $result,
            'personality_profile' => $profile,
            'big_five_summary' => $bigFive
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
    } elseif ($requestedPatientId) {
        // ✅ SECURITY: Verify user has access to this patient
        if (!AuthMiddleware::canAccessPatient($requestedPatientId)) {
            http_response_code(403);
            throw new Exception('Access denied to patient record');
        }
        
        // Get all results for patient
        $sql = "SELECT jfr.* FROM japanese_fy_results jfr
                INNER JOIN user_patient_mapping upm 
                ON jfr.patient_id = upm.patient_id
                WHERE jfr.patient_id = :patient_id 
                AND upm.user_id = :user_id
                ORDER BY jfr.created_at DESC 
                LIMIT 50";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':patient_id', $requestedPatientId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $currentUserId, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Add interpretations to each result
        foreach ($results as &$result) {
            $result['personality_profile'] = PersonalityTraits::getPersonalityProfile($result);
            $result['big_five_summary'] = PersonalityTraits::getBigFiveSummary($result);
        }
        
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'user_id' => $currentUserId,
            'patient_id' => $requestedPatientId,
            'count' => count($results),
            'data' => $results
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
    } else {
        // ✅ SECURITY: Return data for ALL patients user can access
        $allowedPatientIds = AuthMiddleware::getUserPatientIds($currentUserId);
        
        if (empty($allowedPatientIds)) {
            ob_end_clean();
            echo json_encode([
                'success' => true,
                'count' => 0,
                'data' => []
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }
        
        $placeholders = str_repeat('?,', count($allowedPatientIds) - 1) . '?';
        
        $sql = "SELECT * FROM japanese_fy_results 
                WHERE patient_id IN ($placeholders)
                ORDER BY created_at DESC 
                LIMIT 50";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($allowedPatientIds);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($results as &$result) {
            $result['personality_profile'] = PersonalityTraits::getPersonalityProfile($result);
            $result['big_five_summary'] = PersonalityTraits::getBigFiveSummary($result);
        }
        
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'user_id' => $currentUserId,
            'accessible_patients' => $allowedPatientIds,
            'count' => count($results),
            'data' => $results
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
