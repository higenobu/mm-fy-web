namespace App\Utils;

class SentimentAPI
{
    const SENTIMENT_API_URL = 'http://172.17.21.54:8000/analyze';

    public static function analyze(string $text): ?array
    {
        // Prepare the data to send
        $data = json_encode(['texts' => $text]);

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::SENTIMENT_API_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for cURL errors
        if ($response === false) {
            $errorMessage = curl_error($ch);
            curl_close($ch);
            error_log("cURL error: $errorMessage");
            return [
                "sentiment" => "neutral",
                "polarity" => 0.0
            ];
        }

        curl_close($ch);

        // Handle invalid HTTP codes
        if ($httpCode !== 200) {
            error_log("API HTTP Error: $httpCode - Response: $response");
            return [
                "sentiment" => "neutral",
                "polarity" => 0.0
            ];
        }

        // Parse and validate the JSON response
        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($result)) {
            error_log("Invalid JSON Response: $response");
            return [
                "sentiment" => "neutral",
                "polarity" => 0.0
            ];
        }

        // Process the response
     

	 // Extract the response
        if (isset($result['predictions'][0])) {
            return [
                "sentiment" => $result['predictions'][0]['sentiment'] ?? 'neutral',
                "polarity" => $result['predictions'][0]['polarity'] ?? 0.0
            ];
        } else {
            return [
                "sentiment" => "neutral",
                "polarity" => 0.0
            ];
        }
    }
}
