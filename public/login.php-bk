<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Auth\Auth;

// If already logged in, redirect to home
if (Auth::check()) {
    header('Location: /');
    exit;
}

$error = null;

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
error_log("Login attempt - Username: $username");
    $result = Auth::login($username, $password);
error_log("Login result: " . print_r($result, true));
    if ($result['success']) {
        // Redirect to intended page or home
        $redirect = $_GET['redirect'] ?? '/';
        header('Location: ' . $redirect);
        exit;
    } else {
        $error = $result['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Patient Sentiment System</title>
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

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 450px;
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

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
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

        .info {
            margin-top: 25px;
            padding: 20px;
            background: #f0f4ff;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .info h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .info p {
            color: #666;
            font-size: 13px;
            line-height: 1.6;
        }

        .info code {
            background: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>🔐 Login</h1>
        <p class="subtitle">Patient Sentiment Analysis System</p>

        <?php if ($error): ?>
            <div class="error">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login.php<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    required 
                    autofocus
                    placeholder="Enter your username"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    placeholder="Enter your password"
                >
            </div>

            <button type="submit">Login</button>
        </form>

        <div class="info">
            <h3>📋 Default Credentials</h3>
            <p>
                <strong>Username:</strong> <code>admin</code><br>
                <strong>Password:</strong> <code>admin123</code>
            </p>
            <p style="margin-top: 10px; font-size: 12px; color: #999;">
                Please change the password after first login
            </p>
        </div>
        <div class="links" style="margin-top: 20px; text-align: center; font-size: 14px; color: #666;">
            Don't have an account? <a href="/register.php" style="color: #667eea; text-decoration: none; font-weight: 600;">Register here</a>
        </div>
    </div>
</body>
</html>
    </div>
</body>
</html>

