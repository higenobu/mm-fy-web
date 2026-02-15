<?php

namespace App\Controllers;

use App\Models\User;
use App\Utils\SessionManager;

class AuthController
{
    /**
     * Show login form
     */
    public function showLogin(): void
    {
        // If already logged in, redirect to home
        if (SessionManager::isLoggedIn()) {
            header('Location: /');
            exit;
        }
        
        require '../templates/login.php';
    }

    /**
     * Handle login
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $error = null;

        if (empty($username) || empty($password)) {
            $error = 'Username and password are required';
            require '../templates/login.php';
            return;
        }

        $user = User::authenticate($username, $password);

        if ($user) {
            SessionManager::login($user);
            
            // Redirect to intended page or home
            $redirect = $_GET['redirect'] ?? '/';
            header("Location: $redirect");
            exit;
        } else {
            $error = 'Invalid username or password';
            require '../templates/login.php';
        }
    }

    /**
     * Handle logout
     */
    public function logout(): void
    {
        SessionManager::logout();
        header('Location: /login');
        exit;
    }

    /**
     * Show registration form
     */
    public function showRegister(): void
    {
        if (SessionManager::isLoggedIn()) {
            header('Location: /');
            exit;
        }
        
        require '../templates/register.php';
    }

    /**
     * Handle registration
     */
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /register');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $fullName = trim($_POST['full_name'] ?? '');
        $error = null;

        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            $error = 'All fields are required';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters';
        } elseif (User::usernameExists($username)) {
            $error = 'Username already exists';
        } elseif (User::emailExists($email)) {
            $error = 'Email already exists';
        }

        if ($error) {
            require '../templates/register.php';
            return;
        }

        try {
            User::create($username, $email, $password, $fullName);
            
            // Auto-login after registration
            $user = User::authenticate($username, $password);
            if ($user) {
                SessionManager::login($user);
                header('Location: /');
                exit;
            }
        } catch (\Exception $e) {
            $error = 'Registration failed: ' . $e->getMessage();
            require '../templates/register.php';
        }
    }
}
