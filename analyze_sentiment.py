import sys
from textblob import TextBlob

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

if __name__ == "__main__":
    # Take text input from PHP via the command line
    if len(sys.argv) > 1:
        input_text = sys.argv[1]
        sentiment = analyze_sentiment(input_text)
        print(sentiment)  # Output the sentiment label ('positive', 'negative', 'neutral')
    else:
        print("neutral")  # Default sentiment if no input provided
