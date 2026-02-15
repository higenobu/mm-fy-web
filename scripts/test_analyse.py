import requests
import json

# Define the FastAPI endpoint URL
url = "http://172.17.21.54:8000/analyze"  # Replace with your actual endpoint URL

# Input Data: List of strings
texts = ["I am happy", "YOU are happy"]

# Construct the payload as a dictionary
payload = {"texts": texts}

# Send a POST request with JSON data
response = requests.post(url, json=payload)

# Handle the response
if response.status_code == 200:
    # Print the result
    print("API Response:")
    print(json.dumps(response.json(), indent=4))
else:
    print(f"Error: {response.status_code} - {response.text}")
