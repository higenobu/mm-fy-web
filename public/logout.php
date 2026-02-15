<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Auth\Auth;

Auth::logout();
header('Location: /login.php');
exit;

