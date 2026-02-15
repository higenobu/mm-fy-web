import sys
from textblob import TextBlob
'''
def analyze_sentiment(text):
    blob = TextBlob(text)
    polarity = blob.sentiment.polarity
    # Classify sentiment based on polarity score
    if polarity > 0:
        return "positive"
    elif polarity < 0:
        return "negative"
    else:
        return "neutral"
'''
def analyze_sentiment(texts):
    """
    Send the list of texts to the FastAPI /analyze endpoint.

    Args:
        texts (list): List of strings to analyze.

    Returns:
        dict: The API response (predictions for each text).
    """
    # Prepare payload for POST request
    payload = {"texts": texts}

    # Send POST request to the FastAPI server
    response = requests.post(url, json=payload)

    if response.status_code == 200:
        # Success response: return the result
        return response.json()
    else:
        # Error: Print the problem and return None
        print(f"Error: {response.status_code} - {response.text}")
        return None
if __name__ == "__main__":
    # Take text input from PHP via the command line
    if len(sys.argv) > 1:
        input_text = sys.argv[1]
        sentiment = analyze_sentiment(input_text)
        print(sentiment)  # Output the sentiment label ('positive', 'negative', 'neutral')
    else:
        print("neutral")  # Default sentiment if no input provided
