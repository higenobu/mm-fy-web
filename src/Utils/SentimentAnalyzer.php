<?php

namespace App\Utils;

class SentimentAnalyzer
{
    public static function analyze(string $text): string
    {
        // Escape special characters for safe command execution
        $escapedText = escapeshellarg($text);

        // Call the Python script via shell
        $command = "python3 " . __DIR__ . "/../../scripts/analyze_sentiment.py $escapedText";
        $output = shell_exec($command);

        // Trim and sanitize output (e.g., "positive", "negative", "neutral")
        return trim($output) ?? 'neutral';
    }
}
