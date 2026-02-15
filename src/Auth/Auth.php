<?php

namespace App\Auth;

use App\Database\Database;
use PDO;

class Auth
{
    /**
     * Check if user is logged in
     */
    public static function check(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
            return false;
        }

        // Check session timeout (30 minutes)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 1800) {
            self::logout();
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

    /**
     * Require authentication (redirect to login if not logged in)
     */
    public static function require(): void
    {
        if (!self::check()) {
            $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
            header('Location: /login.php?redirect=' . urlencode($currentUrl));
            exit;
        }
    }

    /**
     * Login user
     */
    public static function login(string $username, string $password): array
    {
        try {
            $db = new Database();
            $conn = $db->getConnection();

            $stmt = $conn->prepare("
                SELECT id, username, email, password_hash, full_name, role, is_active 
                FROM users 
                WHERE username = :username AND is_active = true
            ");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Start session
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                session_regenerate_id(true);

                // Store user data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                $_SESSION['last_activity'] = time();

                // Update last login
                $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
                $stmt->execute([':id' => $user['id']]);

                return ['success' => true, 'user' => $user];
            }

            return ['success' => false, 'error' => 'Invalid username or password'];

        } catch (\Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Login failed. Please try again.'];
        }
    }

    /**
     * Logout user
     */
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    }

    /**
     * Get current user
     */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        try {
            $db = new Database();
            $conn = $db->getConnection();

            $stmt = $conn->prepare("
                SELECT id, username, email, full_name, role 
                FROM users 
                WHERE id = :id
            ");
            $stmt->execute([':id' => $_SESSION['user_id']]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get username
     */
    public static function username(): ?string
    {
        return $_SESSION['username'] ?? null;
    }

    /**
     * Get user ID
     */
    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
}

