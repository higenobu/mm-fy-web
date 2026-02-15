<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sentiment History Graph</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Loads Chart.js -->
</head>
<body>
    <h1>Sentiment History Graph</h1>

    <!-- Add the canvas where the graph will render -->
    <canvas id="sentimentChart" width="800" height="400"></canvas>

    <!-- Include your JavaScript code -->
    <script>
        // Fetch data and render graph using Chart.js
        fetch('/memos/sentiment_history_data')
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                const labels = data.map(entry => entry.date);
                const scores = data.map(entry => entry.average_score);

                console.log("Labels for graph:", labels);
                console.log("Scores for graph:", scores);

                const ctx = document.getElementById('sentimentChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Average Sentiment Score',
                            data: scores,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        scales: {
                            x: { title: { display: true, text: 'Date' } },
                            y: { title: { display: true, text: 'Sentiment Score' }, min: -1, max: 1 }
                        }
                    }
                });
            })
            .catch(error => console.error("Error fetching or rendering graph:", error));
    </script>
</body>
</html>
