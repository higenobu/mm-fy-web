<?php
$password = $argv[1] ?? 'password123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: $password\n";
echo "Hash: $hash\n";
echo "\nSQL Command:\n";
echo "INSERT INTO users (username, email, password, role) VALUES\n";
echo "('testuser', 'testuser@example.com', '$hash', 'user');\n";
