<?php


require __DIR__ . '/../../vendor/autoload.php';

use App\Database\Database;

// CLI mode for testing
if (php_sapi_name() === 'cli') {
    echo "🧪 CLI Test Mode\n";
    echo str_repeat('=', 50) . "\n";
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Test query
        $sql = "SELECT COUNT(*) FROM public.japanese_fy_results";
        $count = $conn->query($sql)->fetchColumn();
        
        echo "✅ Database connected\n";
        echo "   Total records: $count\n";
        
        // Get latest 5 records
        $sql = "SELECT id, patient_id, text,a_score,b_score,c_score, created_at 
                FROM public.japanese_fy_results 
                ORDER BY created_at DESC LIMIT 5";
        $results = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\n📊 Latest 5 records:\n";
        foreach ($results as $row) {
            echo sprintf("  ID: %d | Patient: %s | Text: %s\n", 
                $row['id'], 
                $row['patient_id'], 
                mb_substr($row['text'], 0, 30)
            );
var_dump($row);
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
    exit(0);
}

// Rest of your HTTP code...
//header('Content-Type: application/json; charset=utf-8');
// ... continue with your existing code



header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET') {
        $patient_id = $_GET['patient_id'] ?? null;
        
        if ($patient_id) {
            // Get sentiments for specific patient
            $sql = "SELECT 
                        id,
                        patient_id,
                        memo_id,
                        text,
                        a_score,
                        b_score,
                        c_score,
                        d_score,
                        h_score,
                        i_score,
                        j_score,
 			k_score,
                        l_score,
                        m_score,
                        scores_json::text as scores_json,
                        created_at
                    FROM public.japanese_fy_results 
                    WHERE patient_id = :patient_id 
                    ORDER BY created_at DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([':patient_id' => $patient_id]);
        } else {
            // Get all sentiments (limited to last 50)
$sql = "SELECT
                        id,
                        patient_id,
                        memo_id,
                        text,
                        A_score,
                        B_score,
                        C_score,
                        D_score,
                        H_score,
                        I_score,
                        J_score,
                        K_score,
                        L_score,
                        M_score,
                        scores_json::text as scores_json,
                        created_at
                    FROM public.japanese_fy_results
                    ORDER BY created_at DESC LIMIT 50";
           
            
            $stmt = $conn->query($sql);
        }
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
var_dump($results);        
        // Decode JSON for each result
        foreach ($results as &$row) {
            if (isset($row['scores_json'])) {
                $row['scores'] = json_decode($row['scores_json'], true);
            }
        }
        
        echo json_encode([
            'success' => true,
            'count' => count($results),
            'data' => $results
        ], JSON_UNESCAPED_UNICODE);
        
    } elseif ($method === 'POST') {
        // Handle POST request for inserting sentiment
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Invalid JSON input');
        }
        
        // Validate required fields
        if (!isset($input['patient_id']) || !isset($input['text']) || !isset($input['scores'])) {
            throw new Exception('Missing required fields: patient_id, text, scores');
        }
        
        $scores = $input['scores'];
        
        $sql = "INSERT INTO public.japanese_fy_results 
                (patient_id, memo_id, text, A_score, B_score, C_score, 
                 D_score, H_score, I_score, J_score, K_score,L_score,M_score,scores_json) 
                VALUES 
                (:patient_id, :memo_id, :text, :v_a, :v_b, :v_c, :v_d, 
                 :v_h, :v_i, :v_j,:v_k,:v_l,:v_m, :scores_json::jsonb)
                RETURNING id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':patient_id' => $input['patient_id'],
            ':memo_id' => $input['memo_id'] ?? null,
            ':text' => $input['text'],
            ':v_a' => $scores['A'] ?? 0,
            ':v_b' => $scores['B'] ?? 0,
            ':v_c' => $scores['C'] ?? 0,
            ':v_d' => $scores['D'] ?? 0,
            ':v_h' => $scores['H'] ?? 0,
            ':v_i' => $scores['I'] ?? 0,
':v_j' => $scores['J'] ?? 0,
':v_k' => $scores['K'] ?? 0,
':v_l' => $scores['L'] ?? 0,
':v_m' => $scores['M'] ?? 0,
                   ':scores_json' => json_encode($scores, JSON_UNESCAPED_UNICODE)
        ]);
        
        $id = $stmt->fetchColumn();
        
        echo json_encode([
            'success' => true,
            'message' => 'Sentiment result inserted',
            'id' => $id
        ], JSON_UNESCAPED_UNICODE);
        
    } else {
        throw new Exception('Method not allowed');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
