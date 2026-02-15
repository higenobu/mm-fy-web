<?php

namespace App\Utils;

use App\Database\Database;
use PDO;

class SessionManager
{
    /**
     * Start a secure session
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Secure session settings
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
            ini_set('session.cookie_samesite', 'Strict');
            
            session_start();
        }
    }

    /**
     * Login user and create session
     */
    public static function login(array $user): void
    {
        self::start();
        
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Store user data in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Create session token in database for added security
        self::createSessionToken($user['id']);
    }

    /**
     * Logout user and destroy session
     */
    public static function logout(): void
    {
        self::start();
        
        // Remove session token from database
        if (isset($_SESSION['user_id'])) {
            self::destroySessionToken($_SESSION['user_id']);
        }
        
        // Destroy session
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
     * Check if user is logged in
     */
    public static function isLoggedIn(): bool
    {
        self::start();
        
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            return false;
        }
        
        // Check session timeout (30 minutes)
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 1800) {
            self::logout();
            return false;
        }
        
        // Update last activity time
        $_SESSION['login_time'] = time();
        
        return true;
    }

    /**
     * Get current user ID
     */
    public static function getUserId(): ?int
    {
        self::start();
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current username
     */
    public static function getUsername(): ?string
    {
        self::start();
        return $_SESSION['username'] ?? null;
    }

    /**
     * Check if user has role
     */
    public static function hasRole(string $role): bool
    {
        self::start();
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    /**
     * Require login (redirect if not logged in)
     */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Create session token in database
     */
    private static function createSessionToken(int $userId): void
    {
        $pdo = Database::getConnection();
        
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 1800); // 30 minutes
        
        $stmt = $pdo->prepare("
            INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at)
            VALUES (:user_id, :token, :ip, :user_agent, :expires_at)
        ");
        
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR'] ?? '');
        $stmt->bindValue(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '');
        $stmt->bindParam(':expires_at', $expiresAt);
        $stmt->execute();
        
        $_SESSION['session_token'] = $token;
    }

    /**
     * Destroy session token from database
     */
    private static function destroySessionToken(int $userId): void
    {
        $pdo = Database::getConnection();
        
        $stmt = $pdo->prepare("DELETE FROM user_sessions WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Clean expired sessions
     */
    public static function cleanExpiredSessions(): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM user_sessions WHERE expires_at < NOW()");
        $stmt->execute();
    }
}
