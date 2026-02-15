<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memo Application</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
        }
        .button-container {
            padding: 20px;
        }
        .button-container a {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            font-size: 18px;
            text-decoration: none;
            border: 2px solid #007BFF;
            background-color: #007BFF;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }
        .button-container a:hover {
            background-color: white;
            color: #007BFF;
        }
        .user-info {
            position: absolute;
            top: 20px;
            right: 20px;
            text-align: right;
        }
        .logout-btn {
            padding: 8px 16px;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['user_id']) && isset($_SESSION['username'])): ?>
        <div class="user-info">
            <p class="mb-1">Logged in as: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
            <a href="/logout" class="btn btn-danger logout-btn">Logout</a>
        </div>
    <?php endif; ?>
    
    <h1>Welcome to the Memo Application</h1>
    <p>Select an action to get started:</p>

    <div class="button-container">
        <!-- Navigation buttons -->
        <a href="/memos/list">View Memos</a>
        <a href="/memos/create">Create a Memo</a>
        <a href="/memos/get_ptid">Select by Patient_id</a>
    </div>
</body>
</html>
