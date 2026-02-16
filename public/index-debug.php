<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\MemoController;

// Get the requested URI and HTTP method
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
var_dump("Requested URI: $requestUri"); // Print the current route for debugging
error_log('Requested URI: ' . $requestUri);
// Example routes
if ($requestUri === '/memos/list') {
    (new MemoController())->list();
} elseif ($requestUri === '/memos/display') {
    (new MemoController())->display();
} elseif ($requestUri === '/memos/edit') {
    (new MemoController())->edit();
} else {
    http_response_code(404);
    echo "404 Not Found - The requested route does not exist.";
}
