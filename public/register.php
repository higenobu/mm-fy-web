<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Auth\Auth;
use App\Database\Database;

// If already logged in, redirect to home
if (Auth::check()) {
    header('Location: /');
    exit;
}

$error = null;
$success = null;

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');

    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All required fields must be filled';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        try {
            $db = new Database();
            $conn = $db->getConnection();

            // Check if username exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'Username already exists';
            }
            // Check if email exists
            elseif (true) {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
                $stmt->execute([':email' => $email]);
                if ($stmt->fetchColumn() > 0) {
                    $error = 'Email already exists';
                } else {
                    // Create user
                    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                    
                    $stmt = $conn->prepare("
                        INSERT INTO users (username, email, password_hash, full_name, role, is_active)
                        VALUES (:username, :email, :password_hash, :full_name, 'user', true)
                    ");
                    
                    $stmt->execute([
                        ':username' => $username,
                        ':email' => $email,
                        ':password_hash' => $passwordHash,
                        ':full_name' => $fullName
                    ]);

                    $success = 'Account created successfully! You can now login.';
                    
                    // Auto-login after registration
                    Auth::login($username, $password);
                    header('Location: /');
                    exit;
                }
            }

        } catch (\Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $error = 'Registration failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Patient Sentiment System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 500px;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
            font-size: 2em;
        }

        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .error {
            background: #fee;
            color: #c33;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #c33;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #28a745;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
        }

        label .required {
            color: #c33;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .links {
            margin-top: 25px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .password-hint {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>📝 Create Account</h1>
        <p class="subtitle">Patient Sentiment Analysis System</p>

        <?php if ($error): ?>
            <div class="error">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success">
                ✓ <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/register.php">
            <div class="form-group">
                <label for="username">
                    Username <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    required 
                    autofocus
                    placeholder="Choose a username"
                    minlength="3"
                >
            </div>

            <div class="form-group">
                <label for="email">
                    Email <span class="required">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    required
                    placeholder="your.email@example.com"
                >
            </div>

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input 
                    type="text" 
                    id="full_name" 
                    name="full_name" 
                    value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                    placeholder="John Doe (optional)"
                >
            </div>

            <div class="form-group">
                <label for="password">
                    Password <span class="required">*</span>
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    placeholder="Enter password"
                    minlength="6"
                >
                <div class="password-hint">Minimum 6 characters</div>
            </div>

            <div class="form-group">
                <label for="confirm_password">
                    Confirm Password <span class="required">*</span>
                </label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    required
                    placeholder="Confirm password"
                    minlength="6"
                >
            </div>

            <button type="submit">Create Account</button>
        </form>

       
</body>
</html>

