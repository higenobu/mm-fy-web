<?php
require_once '../src/Models/PatientMemo.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Fetch details for the specific memo
    $memo = PatientMemo::getMemoById($id); // Create this function in the PatientMemo model
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Memo</title>
</head>
<body>
    <h1>Edit Memo</h1>
    <?php if ($memo): ?>
        <form method="POST" action="/memos/update">
            <input type="hidden" name="id" value="<?= htmlspecialchars($memo['id']) ?>">

            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($memo['title']) ?>" required><br><br>

            <label for="comment">Content:</label><br>
            <textarea id="comment" name="comment" rows="10" cols="60" required><?= htmlspecialchars($memo['comment']) ?></textarea><br><br>

            <label for="sentiment">Sentiment:</label>
            <select id="sentiment" name="sentiment" required>
                <option value="positive" <?= $memo['sentiment'] === 'positive' ? 'selected' : '' ?>>Positive</option>
                <option value="neutral" <?= $memo['sentiment'] === 'neutral' ? 'selected' : '' ?>>Neutral</option>
                <option value="negative" <?= $memo['sentiment'] === 'negative' ? 'selected' : '' ?>>Negative</option>
            </select><br><br>

            <label for="sentiment_score">Score:</label>
            <input type="number" id="sentiment_score" name="sentiment_score" step="0.01" value="<?= htmlspecialchars($memo['sentiment_score']) ?>" required><br><br>

            <button type="submit">Save Changes</button>
        </form>
    <?php else: ?>
        <p>Memo not found!</p>
    <?php endif; ?>
</body>
</html>
