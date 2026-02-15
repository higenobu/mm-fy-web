from flask import Flask, request, jsonify
from textblob import TextBlob

app = Flask(__name__)

@app.route('/analyze_sentiment', methods=['POST'])
def analyze_sentiment():
    try:
        # Get the JSON data sent by the client
        data = request.get_json()
        text = data.get("text", "")

        # Perform sentiment analysis using TextBlob
        blob = TextBlob(text)
        polarity = blob.sentiment.polarity

        # Determine sentiment category
        if polarity > 0:
            sentiment = "positive"
        elif polarity < 0:
            sentiment = "negative"
        else:
            sentiment = "neutral"

        # Return the result as a JSON response
        return jsonify({"sentiment": sentiment, "polarity": polarity})

    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)  # API will run on port 5000
