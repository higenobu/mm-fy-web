<?php
header('Content-Type: application/json');

// Load config
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

$response = [
    'env_loaded' => false,
    'connection' => false,
    'error' => null
];

// Check environment variables
$response['env'] = [
    'DB_HOST' => getenv('DB_HOST') ?: 'NOT SET',
    'DB_NAME' => getenv('DB_NAME') ?: 'NOT SET',
    'DB_USER' => getenv('DB_USER') ?: 'NOT SET',
    'DB_PASS' => getenv('DB_PASS') ? '***SET***' : 'NOT SET',
    'DB_PORT' => getenv('DB_PORT') ?: '5432'
];

if (getenv('DB_NAME')) {
    $response['env_loaded'] = true;
}

// Try direct PDO connection
try {
    $host = getenv('DB_HOST') ?: 'localhost';
    $dbname = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');
    $port = getenv('DB_PORT') ?: '5432';
    
    if (!$dbname) {
        throw new Exception('DB_NAME not set');
    }
    
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $response['dsn'] = str_replace($pass, '***', $dsn);
    
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $response['connection'] = true;
    
    // Test query
    $count = $pdo->query("SELECT COUNT(*) FROM japanese_fy_results")->fetchColumn();
    $response['record_count'] = $count;
    $response['success'] = true;
    
} catch (PDOException $e) {
    $response['error'] = $e->getMessage();
    $response['error_code'] = $e->getCode();
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
