<?php

namespace App\Controllers;

use App\Database\Database;
use App\Utils\SentimentAPI;

class MemoController
{
    private $db;
    private $conn;

    public function __construct()
    {
        // Load config
        if (file_exists(__DIR__ . '/../../public/api/config.php')) {
            require_once __DIR__ . '/../../public/api/config.php';
        }
        
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendError('Method not allowed', 405);
            return;
        }

        $patient_id = $_POST['patient_id'] ?? null;
        $title = $_POST['title'] ?? '';
        $comment = $_POST['comment'] ?? '';
        $text = $_POST['text'] ?? $comment;

        if (!$patient_id) {
            $this->sendError('Patient ID is required');
            return;
        }

        if (!$text && !$comment) {
            $this->sendError('Text or comment is required');
            return;
        }

        try {
            // Call real sentiment analysis API
            error_log("Analyzing text: " . substr($text, 0, 50) . "...");
            $apiResult = SentimentAPI::analyze($text);
            
            if ($apiResult === null) {
                error_log("SentimentAPI returned null, using fallback scores");
                $scores = $this->generateFallbackScores();
                $normalizedScores = $scores;
            } else {
                error_log("SentimentAPI raw result: " . json_encode($apiResult));
                $normalizedScores = $this->normalizeScores($apiResult);
                error_log("Normalized scores: " . json_encode($normalizedScores));
            }
            
            // Insert into japanese_fy_results
            $sentimentId = $this->insertSentiment($patient_id, $text, $normalizedScores, $apiResult);

            if (!$sentimentId) {
                $this->sendError('Failed to insert sentiment data');
                return;
            }

            $this->sendSuccess([
                'message' => 'Memo created successfully',
                'sentiment_id' => $sentimentId,
                'patient_id' => $patient_id,
                'sentiment' => $normalizedScores,
                'raw_scores' => $apiResult
            ]);

        } catch (\Exception $e) {
            error_log("MemoController error: " . $e->getMessage());
            $this->sendError('Error: ' . $e->getMessage());
        }
    }

    /**
     * Normalize API scores from ~3-5 range to 0-1 range
     * Using min-max normalization
     */
    private function normalizeScores($apiResult): array
    {
        // API returns scores roughly in range 3.0 - 5.5
        // We'll normalize to 0-1 range
        $minScore = 3.0;
        $maxScore = 5.5;
        
        $normalized = [];
        $labels = ['A', 'B', 'C', 'D', 'H', 'I', 'J', 'K', 'L', 'M'];
        
        foreach ($labels as $label) {
            if (isset($apiResult[$label])) {
                $rawScore = (float)$apiResult[$label];
                
                // Min-max normalization: (x - min) / (max - min)
                $normalized[strtolower($label)] = round(
                    max(0, min(1, ($rawScore - $minScore) / ($maxScore - $minScore))),
                    4
                );
            } else {
                $normalized[strtolower($label)] = 0.5; // Default if missing
            }
        }
        
        return $normalized;
    }

    /**
     * Fallback scores if API fails
     */
    private function generateFallbackScores(): array
    {
        return [
            'a' => 0.5,
            'b' => 0.5,
            'c' => 0.5,
            'd' => 0.5,
            'h' => 0.5,
            'i' => 0.5,
            'j' => 0.5,
            'k' => 0.5,
            'l' => 0.5,
            'm' => 0.5
        ];
    }

    private function insertSentiment($patient_id, $text, $scores, $rawApiResult): ?int
    {
        $sql = "INSERT INTO japanese_fy_results
                (patient_id, text, a_score, b_score, c_score, d_score, 
                 h_score, i_score, j_score, k_score, l_score, m_score, scores_json)
                VALUES
                (:patient_id, :text, :a, :b, :c, :d, :h, :i, :j, :k, :l, :m, :scores_json)
                RETURNING id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':patient_id' => $patient_id,
            ':text' => $text,
            ':a' => $scores['a'],
            ':b' => $scores['b'],
            ':c' => $scores['c'],
            ':d' => $scores['d'],
            ':h' => $scores['h'],
            ':i' => $scores['i'],
            ':j' => $scores['j'],
            ':k' => $scores['k'],
            ':l' => $scores['l'],
            ':m' => $scores['m'],
            ':scores_json' => json_encode([
                'normalized_scores' => $scores,
                'raw_api_result' => $rawApiResult
            ], JSON_UNESCAPED_UNICODE)
        ]);

        return $stmt->fetchColumn();
    }

    public function getMemos($patient_id = null): void
    {
        try {
            if ($patient_id) {
                $sql = "SELECT * FROM japanese_fy_results
                        WHERE patient_id = :patient_id
                        ORDER BY created_at DESC";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([':patient_id' => $patient_id]);
            } else {
                $sql = "SELECT * FROM japanese_fy_results 
                        ORDER BY created_at DESC 
                        LIMIT 50";
                $stmt = $this->conn->query($sql);
            }

            $memos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $this->sendSuccess([
                'count' => count($memos),
                'data' => $memos
            ]);

        } catch (\Exception $e) {
            $this->sendError('Error: ' . $e->getMessage());
        }
    }

    public function getMemo($memo_id): void
    {
        try {
            $sql = "SELECT * FROM japanese_fy_results WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $memo_id]);

            $memo = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$memo) {
                $this->sendError('Memo not found', 404);
                return;
            }

            $this->sendSuccess($memo);

        } catch (\Exception $e) {
            $this->sendError('Error: ' . $e->getMessage());
        }
    }

    public function update($memo_id): void
    {
        $this->sendError('Update not implemented yet');
    }

    public function delete($memo_id): void
    {
        try {
            $sql = "DELETE FROM japanese_fy_results WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $memo_id]);

            $this->sendSuccess(['message' => 'Memo deleted successfully']);

        } catch (\Exception $e) {
            $this->sendError('Error: ' . $e->getMessage());
        }
    }

    private function sendSuccess($data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    private function sendError($message, $code = 400): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error' => $message
        ], JSON_UNESCAPED_UNICODE);
    }
}
