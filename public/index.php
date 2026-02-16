<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Utils\SessionManager;

SessionManager::start();
$isLoggedIn = SessionManager::isLoggedIn();
$username = SessionManager::getUsername();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MM-FY Analysis - Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }
        .btn-secondary:hover {
            background: #d0d0d0;
        }
        .content {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .menu-item {
            padding: 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            display: block;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }
        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        .menu-item h3 {
            margin: 0 0 10px 0;
            font-size: 20px;
        }
        .menu-item p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
            line-height: 1.5;
        }
        .welcome {
            color: #667eea;
            font-weight: 600;
            font-size: 16px;
        }
        .feature-list {
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }
        .feature-list li {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .feature-list li:last-child {
            border-bottom: none;
        }
        .hero-section {
            text-align: center;
            padding: 40px 0;
        }
        .hero-section h2 {
            color: #333;
            font-size: 32px;
            margin-bottom: 15px;
        }
        .hero-section p {
            color: #666;
            font-size: 18px;
            line-height: 1.6;
        }
        .cta-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 12px;
            margin-top: 30px;
            text-align: center;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 MM-FY Analysis System</h1>
        <div class="user-info">
            <?php if ($isLoggedIn): ?>
                <span class="welcome">Welcome, <?= htmlspecialchars($username) ?>!</span>
                <a href="/logout.php" class="btn btn-secondary">Logout</a>
            <?php else: ?>
                <a href="/login.php" class="btn btn-primary">Login</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="content">
        <?php if ($isLoggedIn): ?>
            <h2>🎯 Dashboard</h2>
            <p style="color: #666; margin-top: 10px;">Welcome back! Access your personality analysis tools below:</p>
            
            <div class="menu-grid">
                <a href="/historical_graph.html" class="menu-item">
                    <h3>📈 Historical Graph</h3>
                    <p>View your personality scores over time with interactive charts and trend analysis</p>
                </a>
                
                <a href="/sentiment_graph.html" class="menu-item">
                    <h3>📊 Sentiment Analysis</h3>
                    <p>Analyze sentiment patterns and emotional trends with multiple chart types</p>
                </a>
                
                <a href="/personality_profile.html" class="menu-item">
                    <h3>👤 Personality Profile</h3>
                    <p>View detailed Big Five personality insights and trait interpretations</p>
                </a>
                
                <a href="/api/japanese_fy_results.php" class="menu-item" target="_blank">
                    <h3>📄 API Results</h3>
                    <p>View raw data in JSON format for advanced analysis</p>
                </a>
            </div>
        <?php else: ?>
            <div class="hero-section">
                <h2>Welcome to MM-FY Analysis</h2>
                <p>A comprehensive personality analysis system based on the Big Five personality traits<br>
                and advanced Japanese sentiment analysis.</p>
            </div>
            
            <div class="cta-box">
                <h3 style="margin: 0 0 15px 0; font-size: 24px;">Get Started Today</h3>
                <p style="margin: 0 0 20px 0; opacity: 0.9;">Please login to access your personality analysis dashboard</p>
                <a href="/login.php" class="btn btn-primary" style="font-size: 16px; padding: 12px 30px;">
                    Login Now →
                </a>
            </div>
            
            <h3 style="margin-top: 40px; color: #333;">✨ Features:</h3>
            <ul class="feature-list">
                <li><strong>📈 Historical Tracking:</strong> Monitor personality score changes over time</li>
                <li><strong>📊 Sentiment Analysis:</strong> Visualize emotional patterns with multiple chart types</li>
                <li><strong>👤 Personality Profiles:</strong> Detailed Big Five personality trait analysis</li>
                <li><strong>🔒 Secure Access:</strong> Role-based authentication and authorization</li>
                <li><strong>🎨 Interactive Charts:</strong> Beautiful visualizations powered by Chart.js</li>
                <li><strong>📱 Responsive Design:</strong> Works seamlessly on desktop and mobile devices</li>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
