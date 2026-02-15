<?php
require __DIR__ . '/vendor/autoload.php';

use App\Utils\SentimentAPI;

$text = "今日はとても良い気分です。素晴らしい一日でした！";

echo "Testing SentimentAPI with text:\n";
echo "$text\n\n";

echo "Calling API at: " . \App\Utils\SentimentAPI::SENTIMENT_API_URL . "\n\n";

$result = SentimentAPI::analyze($text);

if ($result === null) {
    echo "❌ API returned null (check error_log)\n";
} else {
    echo "✅ API Result:\n";
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    echo "\n\n";
    
    echo "Keys in result:\n";
    foreach (array_keys($result) as $key) {
        echo "  - $key: " . $result[$key] . "\n";
    }
}
