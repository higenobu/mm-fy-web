<?php

namespace App\Middleware;

class AuthMiddleware
{
    /**
     * Check if user is authenticated
     * Redirect to login page if not authenticated
     *
     * @param int $timeout Session timeout in seconds (default: 1800 = 30 minutes)
     */
    public static function check(int $timeout = 1800): void
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            // Store the requested URL to redirect after login (only local paths)
            $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
            // Validate it's a local path to prevent open redirect
            if (strpos($requestUri, '/') === 0 && strpos($requestUri, '//') !== 0) {
                $_SESSION['redirect_after_login'] = $requestUri;
            }
            header('Location: /login');
            exit;
        }
        
        // Check session timeout
        if (isset($_SESSION['last_activity'])) {
            $elapsed = time() - $_SESSION['last_activity'];
            
            if ($elapsed > $timeout) {
                // Session expired
                session_unset();
                session_destroy();
                session_start();
                $_SESSION['error'] = 'Your session has expired. Please login again.';
                header('Location: /login');
                exit;
            }
        }
        
        // Update last activity time
        $_SESSION['last_activity'] = time();
    }

    /**
     * Check if user is already logged in
     * Redirect to home if logged in (for login/register pages)
     */
    public static function guest(): void
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
    }
}
