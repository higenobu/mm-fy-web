
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../vendor/autoload.php';

use App\Controllers\MemoController;
use App\Models\PatientMemo;
use App\Database\Database;

try {
    echo "Testing MemoController...\n\n";
    
    echo "✓ Classes loaded successfully\n\n";
    
    echo "Testing database connection...\n";
    $pdo = Database::getConnection();
    echo "✓ Database connected\n\n";
    
    echo "Testing PatientMemo::getAll()...\n";
    $memos = PatientMemo::getAll();
    echo "✓ Got " . count($memos) . " memos\n\n";
    
    echo "Testing MemoController...\n";
    $controller = new MemoController();
    echo "✓ Controller created\n\n";
    
    echo "All tests passed!\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

