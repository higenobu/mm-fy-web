<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Memo</title>
</head>
<body>
    <h1>Create New Memo</h1>
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

<form id="createMemoForm" method="POST" action="/memos/create">
<label for="patient_id">Patient_id:</label>
    <input type="text" id="patient_id" name="patient_id" required>
    <br>
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" required>
    <br>

    <label for="comment">Comment:</label>
    <textarea id="comment" name="comment" required></textarea>
    <br>

    <button type="submit">Save</button>
</form>


    <br>
    <a href="/memos/list">Back to Memo List</a>
</body>
</html>
