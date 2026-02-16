<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Utils\SessionManager;

// Logout the user
SessionManager::logout();

// Redirect to home
header('Location: /');
exit;
