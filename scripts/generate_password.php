#!/usr/bin/env php
<?php

/**
 * Generate password hash for database insertion
 * Usage: php scripts/generate_password.php [password]
 */

$password = $argv[1] ?? null;

if (!$password) {
    echo "Usage: php scripts/generate_password.php [password]\n";
    echo "Example: php scripts/generate_password.php password123\n";
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

echo "\n";
echo "Password: $password\n";
echo "Hash:     $hash\n";
echo "\n";
echo "SQL INSERT Example:\n";
echo "-------------------\n";
echo "INSERT INTO users (username, email, password, role) VALUES\n";
echo "('testuser', 'testuser@example.com', '$hash', 'user');\n";
echo "\n";
