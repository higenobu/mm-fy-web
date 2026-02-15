<?php

    
    require __DIR__ . '/../../vendor/autoload.php';
    
    use App\Database\Database;
    
    $db = new Database();
    $conn = $db->getConnection();
    
    echo json_encode(['step' => 4, 'message' => 'Database connected']);
    

?>
