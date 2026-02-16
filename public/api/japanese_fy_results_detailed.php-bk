<?php
ob_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate');

require __DIR__ . '/../../vendor/autoload.php';

if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

use App\Database\Database;
use App\Utils\PersonalityTraits;

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $patient_id = $_GET['patient_id'] ?? null;
    $result_id = $_GET['id'] ?? null;
    
    if ($result_id) {
        // Get specific result
        $sql = "SELECT * FROM japanese_fy_results WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $result_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            throw new Exception('Result not found');
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
        
    } elseif ($patient_id) {
        // Get all results for patient
        $sql = "SELECT * FROM japanese_fy_results 
                WHERE patient_id = :patient_id 
                ORDER BY created_at DESC 
                LIMIT 50";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':patient_id', $patient_id, PDO::PARAM_INT);
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
            'count' => count($results),
            'data' => $results
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
    } else {
        throw new Exception('Please provide patient_id or id parameter');
    }
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
