<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\MemoController;

// Handle POST and GET requests
$controller = new MemoController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->processForm();
} else {
    $controller->showForm();
}
