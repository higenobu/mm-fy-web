<?php

//require_once '../src/Models/PatientMemo.php'; // Include the necessary model class
use App\Models\PatientMemo; // Bring the PatientMemo class into scope

// Initialize variables for filtering
$filters = [
    'patient_id' => $_GET['patient_id'] ?? null,
    'title' => $_GET['title'] ?? null,
	'comment' => $_GET['comment'] ?? null,
    'sentiment' => $_GET['sentiment'] ?? null,
];

// Fetch filtered memos
$memos = PatientMemo::getFilteredMemos($filters); // Adjust the query based on filters

?>


<!DOCTYPE html>
<html lang="en">
<head>

    <title>List of Patient Memos</title>
    <script src="/js/app.js" defer></script> <!-- Include JavaScript for delete functionality -->
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .filter-form {
            margin-bottom: 20px;
        }
        .filter-form input, .filter-form select {
            margin-right: 10px;
            padding: 5px 10px;
            font-size: 14px;
        }
        .filter-form button {
            padding: 5px 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>

    

<body>


    <h1>Patient Memo List</h1>

    <!-- QBE (Query By Example) Form -->
    <form method="GET" action="/memos/list" class="filter-form">
        <label for="patient_id">Patient ID:</label>
        <input type="text" id="patient_id" name="patient_id" value="<?= htmlspecialchars($filters['patient_id'] ?? '') ?>">

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($filters['title'] ?? '') ?>">
	<label for="comment">comment:</label>
        <input type="text" id="comment" name="comment" value="<?= htmlspecialchars($filters['comment'] ?? '') ?>">
        <label for="sentiment">Sentiment:</label>
        <select id="sentiment" name="sentiment">
            <option value="">All</option>
            <option value="positive" <?= $filters['sentiment'] === 'positive' ? 'selected' : '' ?>>Positive</option>
            <option value="neutral" <?= $filters['sentiment'] === 'neutral' ? 'selected' : '' ?>>Neutral</option>
            <option value="negative" <?= $filters['sentiment'] === 'negative' ? 'selected' : '' ?>>Negative</option>
        </select>

        <button type="submit">Filter</button>
    </form>

    <!-- Memo List Table -->
    <table>
        <thead>
            <tr>
                <th>Patient ID</th>
                <th>Title</th>
	<th>Comment</th>
                <th>Created Date</th>
                <th>Sentiment</th>
                <th>Score</th>
		<th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($memos)): ?>
                <?php foreach ($memos as $memo): ?>
                    <tr>
                        <td>
                        <a href="/memos/display?id=<?= $memo['id'] ?>">
                            <?= htmlspecialchars($memo['patient_id']) ?>
                        </a>
                    </td>
                        
                        <td><?= htmlspecialchars($memo['title']) ?></td>
                        <td>
                        <?= htmlspecialchars($memo['comment'] ?? 'n/a') ?></td>
                        <td><?= htmlspecialchars($memo['created_at']) ?></td>
                        <td><?= htmlspecialchars($memo['sentiment']) ?></td>
                        <td><?= htmlspecialchars($memo['sentiment_score']) ?></td>
                        <td>
            <!-- Edit Button -->
                        <a href="/memos/edit?id=<?= $memo['id'] ?>" class="edit-button" style="text-decoration:none;">
                            <button class="edit-button">Edit</button>
                        </a>
                        <button class="delete-button" data-id="<?= $memo['id'] ?>">Delete</button>
                    </td>
                
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No memos found for the given filters.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>





    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Attach click event listeners to all delete buttons
            const deleteButtons = document.querySelectorAll(".delete-button");

            deleteButtons.forEach(button => {
                button.addEventListener("click", async () => {
                    const memoId = button.dataset.id;

                    if (confirm("Are you sure you want to delete this memo?")) {
                        try {
                            const response = await fetch(`/memos/delete`, {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify({ id: memoId })
                            });

                            if (response.ok) {
                                alert("Memo deleted successfully!");
                                button.closest("tr").remove(); // Remove the memo from the table
                            } else {
                                const error = await response.json();
                                alert(`Error deleting memo: ${error.message}`);
                            }
                        } catch (err) {
                            console.error("Error deleting memo:", err);
                            alert("An error occurred while trying to delete the memo.");
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
