<!DOCTYPE html>
<html lang="en">
<head>
    <title>Patient Memos</title>
    <script src="/js/app.js" defer></script> <!-- Include JavaScript for delete functionality -->
</head>
<body>
    <h1>List of Patient Memos</h1>
<!-- Go Back to Main -->
    <a href="/" style="
        display: inline-block;
        margin-bottom: 20px;
        padding: 10px 15px;
        font-size: 16px;
        background-color: #007BFF;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    ">Go Back to Main</a>
    
    <ul id="memoList">

<ul>
    <?php foreach ($memos as $memo): ?>
        <li>
            <a href="/memos/display?id=<?= $memo['id'] ?>">
                <?= htmlspecialchars($memo['title']) ?>
            </a>
            <span style="margin-left:10px; color:gray;">
                (Sentiment: <?= htmlspecialchars($memo['sentiment'] ?? 'n/a') ?>)
            </span>
<!-- Delete button for the memo -->
                <button class="delete-button" data-id="<?= $memo['id'] ?>">Delete</button>
        </li>
    
        <?php endforeach; ?>
    </ul>
</body>
</html>
