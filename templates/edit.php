<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Memo</title>
</head>
<body>
    <h1>Edit Memo</h1>
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

    <form method="POST" action="/memos/edit">
        <input type="hidden" name="id" value="<?= htmlspecialchars($memo['id']) ?>">
        
        <label for="title">Title:</label>
        <input 
            type="text" 
            id="title" 
            name="title" 
            value="<?= htmlspecialchars($memo['title'] ?? '') ?>" 
            required
        >

        <label for="comment">Comment:</label>
        <textarea 
            id="comment" 
            name="comment" 
            required
        ><?= htmlspecialchars($memo['comment'] ?? '') ?></textarea>

        <button type="submit">Save</button>
    </form>
</body>
</html>
