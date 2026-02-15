<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sentiment Analysis Results</title>
</head>
<body>
    <h1>Sentiment Analysis Results</h1>
    <p><strong>Text:</strong> <?= htmlspecialchars($_POST['text']) ?></p>

    <h2>Sentiment Prediction:</h2>
    <ul>
        <?php foreach ($sentimentResult as $key => $value): ?>
            <li><?= htmlspecialchars($key) ?>: <?= htmlspecialchars($value) ?></li>
        <?php endforeach; ?>
    </ul>

    <a href="index.php">Analyze Another Text</a>
</body>
</html>
