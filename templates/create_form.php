<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sentiment Analysis</title>
</head>
<body>
    <h1>Text Sentiment Analysis</h1>
    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
        <label for="textInput">Enter your text:</label><br>
        <textarea id="textInput" name="text" rows="4" cols="50" placeholder="Type your text here..." required></textarea>
        <br>
        <button type="submit">Analyze Sentiment</button>
    </form>


</body>
</html>
