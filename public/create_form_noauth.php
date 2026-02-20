<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Create Memo</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        textarea { min-height: 150px; font-family: inherit; }
        button { background: #007bff; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
        .result { margin-top: 20px; padding: 20px; background: white; border-radius: 5px; display: none; border: 2px solid #28a745; }
        .result.error { border-color: #dc3545; }
        .btn { display: inline-block; margin-right: 10px; margin-top: 15px; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .btn:hover { background: #218838; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        h1 { color: #333; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>📝 Create Patient Memo with Sentiment Analysis</h1>

    <form id="memoForm">
        

        <div class="form-group">
            <label>📌 Title (optional):</label>
            <input type="text" name="title" placeholder="Enter memo title">
        </div>

        <div class="form-group">
            <label>💬 Comment/Text:</label>
            <textarea name="comment" required placeholder="Enter patient memo text for sentiment analysis...">今日はとても良い気分です。素晴らしい一日でした！</textarea>
        </div>

        <button type="submit">🚀 Create & Analyze</button>
    </form>

    <div id="result" class="result"></div>

    <script>
        document.getElementById('memoForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const resultDiv = document.getElementById('result');
            
            resultDiv.style.display = 'block';
            resultDiv.className = 'result';
            resultDiv.innerHTML = '<p>⏳ Creating memo and analyzing sentiment...</p>';

            try {
                const response = await fetch('/api/memo.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                console.log('Result:', result);

                if (result.success) {
                    const data = result.data;
                    const patientId = formData.get('patient_id');
                    
                    resultDiv.innerHTML = `
                        <h3>✅ Success!</h3>
                        <p><strong>Patient ID:</strong> ${data.patient_id || patientId}</p>
                        <p><strong>Sentiment Analysis ID:</strong> ${data.sentiment_id}</p>
                        <h4>📊 Sentiment Scores:</h4>
                        <pre>${JSON.stringify(data.sentiment, null, 2)}</pre>
                        <div>
                            <a href="/sentiment_graph.html?patient_id=${patientId}" class="btn">📊 View Graph</a>
                            <a href="/create_form_noauth.php" class="btn btn-secondary">➕ Create Another</a>
                        </div>
                    `;
                    
                    // Clear form
                    document.querySelector('textarea[name="comment"]').value = '';
                    document.querySelector('input[name="title"]').value = '';
                    
                } else {
                    resultDiv.className = 'result error';
                    resultDiv.innerHTML = `
                        <h3>❌ Error</h3>
                        <p>${result.error}</p>
                        <pre>${JSON.stringify(result, null, 2)}</pre>
                    `;
                }

            } catch (error) {
                console.error('Error:', error);
                resultDiv.className = 'result error';
                resultDiv.innerHTML = `
                    <h3>❌ Error</h3>
                    <p>${error.message}</p>
                `;
            }
        });
    </script>
</body>
</html>
