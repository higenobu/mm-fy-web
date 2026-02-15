<?php
require_once __DIR__ . '/src/Utils/SentimentAPI.php';

use App\Utils\SentimentAPI;

// Test with example text
$text = "I absolutely love FastAPI!";
$response = SentimentAPI::analyze($text);

if ($response) {
    echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . PHP_EOL;
} else {
    echo "No valid response from SentimentAPI." . PHP_EOL;
}
