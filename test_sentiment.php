<?php
require __DIR__ . '/vendor/autoload.php'; 
//require 'SentimentAPI.php';

use App\Utils\SentimentAPI;

// Client code
$comment = "I absolutely love FastAPI!";
$result = SentimentAPI::analyze($comment);
echo json.encode($result,true);
if ($result) {
    echo "Sentiment: " . $result['sentiment'] . PHP_EOL;
    echo "Polarity: " . $result['polarity'] . PHP_EOL;
} else {
    echo "Error analyzing sentiment." . PHP_EOL;
}

//require __DIR__ . '/vendor/autoload.php';

//use App\Utils\SentimentAnalyzer;

//echo SentimentAnalyzer::analyze("This app is amazing!");
