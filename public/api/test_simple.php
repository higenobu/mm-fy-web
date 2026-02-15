<?php
header('Content-Type: application/json');

// Test 1: Basic JSON
echo json_encode([
    'success' => true,
    'message' => 'API is working',
    'test' => 'OK'
]);
?>
