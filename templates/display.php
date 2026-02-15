<!DOCTYPE html>
<html lang="en">
<head>
    <title>Memo Details</title>
    <style>
        /* Basic Table Styling */
        table {
            width: 50%; /* Adjust width as needed */
            border-collapse: collapse;
            margin: 20px auto; /* Center on the page */
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        a {
            color: #007BFF;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .back-button {
            display: inline-block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Memo Details</h1>

    <!-- Go Back Button -->
    <a href="/memos/list" class="back-button">Back to Memo List</a>

    <!-- Memo Details Table -->
    <table>
        <tbody>
	<tr>
                <th>Patient_id</th>
                <td><?= htmlspecialchars($memo['patient_id'] ?? 'N/A') ?></td>
            </tr>
            <tr>
                <th>Title</th>
                <td><?= htmlspecialchars($memo['title'] ?? 'N/A') ?></td>
            </tr>
            <tr>
                <th>Content</th>
                <td><?= htmlspecialchars($memo['comment'] ?? 'N/A') ?></td>
            </tr>
            <tr>
                <th>Created Date</th>
                <td><?= htmlspecialchars($memo['created_at'] ?? 'N/A') ?></td>
            </tr>
            <tr>
                <th>Sentiment</th>
                <td><?= htmlspecialchars($memo['sentiment'] ?? 'N/A') ?></td>
            </tr>
            <tr>
                <th>Sentiment Score</th>
                <td><?= htmlspecialchars($memo['sentiment_score'] ?? 'N/A') ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
