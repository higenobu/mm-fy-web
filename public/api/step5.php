<?php
//header('Content-Type: application/json');

    
    require __DIR__ . '/../../vendor/autoload.php';
    
    use App\Database\Database;
    
    $db = new Database();
    $conn = $db->getConnection();
    
    $count = $conn->query("SELECT * FROM japanese_fy_results")->fetchColumn();
    
//    echo json_encode(['step' => 5, 'message' => 'Query worksaaaaa'],'memo' => $value]);
var_dump($count);    

?>
