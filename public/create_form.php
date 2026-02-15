<?php
require __DIR__ . '/../vendor/autoload.php';
use App\Auth\Auth;
Auth::require();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Create Memo</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        textarea { min-height: 150px; }
        button { background: #007bff; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
        .result { margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; display: none; }
        .btn { display: inline-block; margin-right: 10px; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; }
        .btn:hover { background: #218838; }
    </style>
</head>
<body>
    <h1>📝 Create Patient Memo</h1>

    <form id="memoForm">
        <div class="form-group">
            <label>Patient ID:</label>
            <input type="number" name="patient_id" required value="2">
        </div>

        <div class="form-group">
            <label>Title:</label>
            <input type="text" name="title" required placeholder="Enter memo title">
        </div>

        <div class="form-group">
            <label>Comment:</label>
            <textarea name="comment" required placeholder="Enter patient memo text..."></textarea>
        </div>

        <button type="submit">Create Memo with Sentiment Analysis</button>
    </form>

    <div id="result" class="result"></div>

    <script>
        document.getElementById('memoForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const resultDiv = document.getElementById('result');
            
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<p>⏳ Creating memo and analyzing sentiment...</p>';

            try {
                const response = await fetch('/api/memo.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    resultDiv.innerHTML = `
                        <h3>✅ Success!</h3>
                        <p><strong>Memo ID:</strong> ${result.data.memo_id}</p>
                        <p><strong>Sentiment ID:</strong> ${result.data.sentiment_id || 'N/A'}</p>
                        <h4>Sentiment Scores:</h4>
                        <pre>${JSON.stringify(result.data.sentiment || result.data, null, 2)}</pre>
                        <br>
                        <a href="/index.php?patient_id=${formData.get('patient_id')}" class="btn">← Back to Dashboard</a>
                        <a href="/sentiment_graph.html?patient_id=${formData.get('patient_id')}" class="btn">📊 View Graph</a>
                    `;
                    e.target.reset();
                } else {
                    resultDiv.innerHTML = `<h3>❌ Error</h3><p>${result.error}</p>`;
                }

            } catch (error) {
                console.error('Error:', error);
                resultDiv.innerHTML = `<h3>❌ Error</h3><p>${error.message}</p>`;
            }
        });
    </script>
</body>
</html>
