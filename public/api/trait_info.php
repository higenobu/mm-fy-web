<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require __DIR__ . '/../../vendor/autoload.php';

use App\Utils\PersonalityTraits;

// Get all traits information
$traits = PersonalityTraits::getAllTraits();

echo json_encode([
    'success' => true,
    'traits' => $traits,
    'description' => 'Big Five Personality Traits - Japanese 10-item inventory'
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
