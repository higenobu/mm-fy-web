<?php

namespace App\Models;

use App\Database\Database;
//use App\Utils\SentimentAnalyzer;
use App\Utils\SentimentAPI;
use PDO;

class PatientMemo
{
 public static function getFilteredMemos(array $filters): array
    {
        $pdo = Database::getConnection();

        $sql = "SELECT patient_id, title, comment,created_at, sentiment, sentiment_score
                FROM patient_memos WHERE 1=1";

        $params = [];
        if (!empty($filters['patient_id'])) {
            $sql .= " AND patient_id = :patient_id";
            $params[':patient_id'] = $filters['patient_id'];
        }
        if (!empty($filters['title'])) {
            $sql .= " AND title LIKE :title";
            $params[':title'] = '%' . $filters['title'] . '%';
        }
if (!empty($filters['comment'])) {
            $sql .= " AND comment LIKE :comment";
            $params[':comment'] = '%' . $filters['comment'] . '%';
        }        
if (!empty($filters['sentiment'])) {
            $sql .= " AND sentiment = :sentiment";
            $params[':sentiment'] = $filters['sentiment'];
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public static function getMemosByPatientId(string $patientId): array
    {
        // Get the PDO database connection
        $pdo = Database::getConnection();

        // Query to fetch memos for a specific patient
        $sql = "SELECT id,patient_id,title, comment, created_at, sentiment, sentiment_score
                FROM patient_memos
                WHERE patient_id = :patient_id
                ORDER BY created_at DESC";

        // Prepare the SQL statement
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':patient_id', $patientId, PDO::PARAM_STR);

        // Execute the statement and return the results
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all patient memos.
     */
    public static function getAll(): array
    {
	// Analyze sentiment with Python
        //$sentiment = SentimentAnalyzer::analyze($comment);
        $pdo = Database::getConnection();
        // $stmt = $pdo->query("SELECT * FROM patient_memos ORDER BY created_at DESC");
$sql = "SELECT id,patient_id, title, created_at,comment,sentiment, sentiment_score FROM patient_memos ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single patient memo by ID.
     */
    public static function getById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM patient_memos WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $memo = $stmt->fetch(PDO::FETCH_ASSOC);
        return $memo ?: null;
    }

    /**
     * Create a new patient memo.
     */
    public static function create(string $patient_id,string $title, string $comment, string $sent): void
    {
	// Analyze sentiment with Python
    
    
        $pdo = Database::getConnection();

        // Get sentiment and polarity from Python API
        // Call the SentimentAPI to analyze the comment
    //$jsonString = SentimentAPI::analyze($comment);
//$sentimentData = json_decode($jsonString, true);
//var_dump($sentimentData);

    // Handle the SentimentAPI response properly
  //  if (!empty($sentiment) && is_array($sentiment)) {
    //    $sentimentValue = $sentiment['sentiment'] ?? 'no sentiment';
      //  $polarityValue = $sentiment['polarity'] ?? 0.0;
	//$sentimentData = json_encode($sentiment);
    //} else {
       // $sentimentValue = 'xx'; // Default value
        //$polarityValue = 0.1;       // Default polarity
    //}
     //echo  $sentimentData;
    // Example: Insert into the database
    // Assuming `sentiment_value` and `polarity_value` are columns in the `patient_memos` table
    //$db = Database::getConnection();
    //$stmt = $db->prepare("
    //    INSERT INTO patient_memos (patient_id, title, comment, sentiment_value, polarity_value)
    //    VALUES (:patient_id, :title, :comment, :sentiment_value, :polarity_value)
    //");

    //$stmt->execute([
      //  ':patient_id' => $patient_id,
       // ':title' => $title,
       // ':comment' => $comment,
       // ':sentiment_value' => $sentimentValue,
       // ':polarity_value' => $polarityValue
    //]);



	//$analysis = SentimentAPI::analyze($comment);
        //echo json.encode($analysis);
	//if (!empty($analysis) && is_array($analysis)) {
    	//	$sentimentValue = $analysis['sentiment'] ?? 'neutral';
	//} else {
    	//	$sentimentValue = 'neutral';
	//}
	//$sentiment = $analysis['sentiment'] ?? 'neutral';
        //$sentimentScore = "0";

        // Insert memo into the database
$pvalue='0';
        $sql = "INSERT INTO patient_memos (patient_id,title, comment, created_at,sentiment, sentiment_score) 
                VALUES (:patient_id, :title, :comment,:created_at, :sentiment_value, :polarity_score)";
        $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':patient_id', $patient_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':comment', $comment);
	$stmt->bindValue(':created_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindParam(':sentiment_value', $sent);
        $stmt->bindParam(':polarity_score', $$pvalue);
        $stmt->execute();
    }

    

    /**
     * Update an existing patient memo.
     */
public static function update(int $id, string $title, string $comment): void
    {
        $pdo = Database::getConnection();

        // Get sentiment and polarity from Python API
        $analysis = SentimentAPI::analyze($comment);
        $sentiment = $analysis['sentiment'];
        $sentimentScore = $analysis['polarity'];

        // Update memo in the database
        $sql = "UPDATE patient_memos 
                SET title = :title, comment = :comment, 
                    sentiment = :sentiment, sentiment_score = :sentiment_score,
                    updated_at = NOW()
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':sentiment', $sentiment);
        $stmt->bindParam(':sentiment_score', $sentimentScore);
        $stmt->execute();
    
        }

    /**
     * Delete a patient memo.
     */
    public static function delete(int $id): void
    {
        $pdo = Database::getConnection();
// echo "Deleting memo with ID = $id";
        $stmt = $pdo->prepare("DELETE FROM patient_memos WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

public static function getSentimentHistory(): array
    {
        $pdo = Database::getConnection();

        $sql = "SELECT created_at::DATE AS date, CAST(AVG(sentiment_score) AS FLOAT) AS average_score
                FROM patient_memos 
                GROUP BY created_at::DATE
                ORDER BY created_at::DATE ASC";

        $result = $pdo->query($sql);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

public static function updateMemo(array $updatedMemo): bool
{
    $pdo = Database::getConnection();

    $sql = "UPDATE patient_memos 
            SET title = :title,
                comment = :comment,
                sentiment = :sentiment,
                sentiment_score = :sentiment_score
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':title', $updatedMemo['title'], PDO::PARAM_STR);
    $stmt->bindValue(':comment', $updatedMemo['comment'], PDO::PARAM_STR);
    $stmt->bindValue(':sentiment', $updatedMemo['sentiment'], PDO::PARAM_STR);
    $stmt->bindValue(':sentiment_score', $updatedMemo['sentiment_score'], PDO::PARAM_STR);
    $stmt->bindValue(':id', $updatedMemo['id'], PDO::PARAM_INT);

    return $stmt->execute();
}
}
