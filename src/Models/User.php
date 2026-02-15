<?php

namespace App\Models;

use App\Database\Database;
use PDO;

class User
{
    /**
     * Authenticate user by username and password
     */
    public static function authenticate(string $username, string $password): ?array
    {
        $pdo = Database::getConnection();
        
        $stmt = $pdo->prepare("
            SELECT id, username, email, password_hash, full_name, role, is_active 
            FROM users 
            WHERE username = :username AND is_active = true
        ");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Update last login
            self::updateLastLogin($user['id']);
            
            // Remove password hash from returned data
            unset($user['password_hash']);
            return $user;
        }
        
        return null;
    }

    /**
     * Create a new user
     */
    public static function create(string $username, string $email, string $password, string $fullName = '', string $role = 'user'): int
    {
        $pdo = Database::getConnection();
        
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, full_name, role) 
            VALUES (:username, :email, :password_hash, :full_name, :role)
            RETURNING id
        ");
        
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $passwordHash);
        $stmt->bindParam(':full_name', $fullName);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }

    /**
     * Get user by ID
     */
    public static function getById(int $id): ?array
    {
        $pdo = Database::getConnection();
        
        $stmt = $pdo->prepare("
            SELECT id, username, email, full_name, role, is_active, created_at, last_login 
            FROM users 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Update last login timestamp
     */
    private static function updateLastLogin(int $userId): void
    {
        $pdo = Database::getConnection();
        
        $stmt = $pdo->prepare("
            UPDATE users 
            SET last_login = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Check if username exists
     */
    public static function usernameExists(string $username): bool
    {
        $pdo = Database::getConnection();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Check if email exists
     */
    public static function emailExists(string $email): bool
    {
        $pdo = Database::getConnection();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
}
