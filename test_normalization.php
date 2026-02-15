<?php
// Test normalization
$rawScores = [
    "A" => 4.5520339012146,
    "B" => 3.5490269660949707,
    "C" => 3.258517026901245,
    "D" => 4.711277484893799,
    "H" => 5.057178020477295,
    "I" => 3.9300296306610107,
    "J" => 3.8134958744049072,
    "K" => 4.176835060119629,
    "L" => 4.507663249969482,
    "M" => 3.2952215671539307
];

$minScore = 3.0;
$maxScore = 5.5;

echo "Raw scores → Normalized (0-1):\n";
echo "================================\n";

foreach ($rawScores as $label => $raw) {
    $normalized = round(($raw - $minScore) / ($maxScore - $minScore), 4);
    echo "$label: $raw → $normalized\n";
}
