#!/bin/bash

echo "Creating test records..."

texts=(
    "今日はとても楽しい一日でした！"
    "少し心配なことがあります。"
    "素晴らしいニュースを聞きました。"
    "天気が良くて気分が最高です。"
    "困難な状況ですが、頑張ります。"
)

for i in "${!texts[@]}"; do
    echo "Creating record $((i+1))..."
    curl -s -X POST http://localhost:8000/api/memo.php \
      -F "patient_id=2" \
      -F "title=Test Memo $((i+1))" \
      -F "comment=${texts[$i]}" \
      | jq -r '.data.sentiment_id // "failed"'
done

echo ""
echo "✅ Done! View at: http://localhost:8000/sentiment_graph.html?patient_id=2"
