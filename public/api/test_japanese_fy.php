<?php
/**
 * Test script for japanese_fy_results.php
 */

$apiUrl = 'http://localhost:8000/api/japanese_sentiment.php';

echo "🧪 Testing Japanese FY Results API\n";
echo str_repeat('=', 60) . "\n\n";

// Test 1: GET all results
echo "Test 1: GET all results\n";
echo str_repeat('-', 60) . "\n";

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Success\n";
    echo "   Count: {$data['count']}\n";
    echo "   First record ID: " . ($data['data'][0]['id'] ?? 'N/A') . "\n";
} else {
    echo "❌ Failed: HTTP $httpCode\n";
    echo "   Response: $response\n";
}

echo "\n";

// Test 2: GET by patient_id
echo "Test 2: GET by patient_id=1\n";
echo str_repeat('-', 60) . "\n";

$ch = curl_init($apiUrl . '?patient_id=1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Success\n";
    echo "   Count for patient 1: {$data['count']}\n";
} else {
    echo "❌ Failed: HTTP $httpCode\n";
}

echo "\n";

// Test 3: POST new sentiment
//echo "Test 3: POST new sentiment\n";
//echo str_repeat('-', 60) . "\n";
$testPatientId = 2;
//$testMemoId = rand(1000, 9999);
$testData = [
'patient_id' => $testPatientId,  // INTEGER, not string
'memo_id' => null,  // Set to null to avoid foreign key constraint    
'text' => 'これはテストデータです。素晴らしい一日でした。',
    'scores' => [
        'A' => 0.8765,
        'B' => 0.1234,
        'C' => 0.2345,
        'D' => 0.0123,
        'H' => 0.4567,
        'I' => 0.3456,
        'J' => 0.5678,
        'K' => 0.6789,
        'L' => 0.7890,
        'M' => 0.8901
    ]
];

$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($testData),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json']
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Success\n";
    echo "   Inserted ID: {$data['id']}\n";
    echo "   Message: {$data['message']}\n";
} else {
    echo "❌ Failed: HTTP $httpCode\n";
    echo "   Response: $response\n";
}

echo "\n";
echo "✅ All tests complete!\n";
