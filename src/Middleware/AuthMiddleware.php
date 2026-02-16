<?php

namespace App\Middleware;

use App\Utils\SessionManager;

class AuthMiddleware
{
    /**
     * Check if user is logged in
     * Redirect to login if not
     */
    public static function requireAuth(): void
    {
        SessionManager::start();
        
        if (!SessionManager::isLoggedIn()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Authentication required',
                'redirect' => '/login.php'
            ]);
            exit;
        }
    }
    
    /**
     * Get current user's ID
     */
    public static function getUserId(): ?int
    {
        return SessionManager::getUserId();
    }
    
    /**
     * Check if user has access to patient record
     */
    public static function canAccessPatient(int $patientId): bool
    {
        $userId = self::getUserId();
        
        if (!$userId) {
            return false;
        }
        
        // Check if user owns this patient record
        // Implement your business logic here
        return self::hasPatientAccess($userId, $patientId);
    }
    
    /**
     * Check database for user-patient access
     */
    private static function hasPatientAccess(int $userId, int $patientId): bool
    {
        try {
            $db = new \App\Database\Database();
            $conn = $db->getConnection();
            
            // Option A: Direct match (user_id = patient_id)
            // Uncomment if using this approach
            // return $userId === $patientId;
            
            // Option B: Check mapping table
            $sql = "SELECT COUNT(*) FROM user_patient_mapping 
                    WHERE user_id = :user_id AND patient_id = :patient_id";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':patient_id' => $patientId
            ]);
            
            return $stmt->fetchColumn() > 0;
            
        } catch (\Exception $e) {
            error_log("Access check failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all patient IDs the user can access
     */
    public static function getUserPatientIds(int $userId): array
    {
        try {
            $db = new \App\Database\Database();
            $conn = $db->getConnection();
            
            // Option A: User can only access their own ID
            // return [$userId];
            
            // Option B: Get from mapping table
            $sql = "SELECT patient_id FROM user_patient_mapping 
                    WHERE user_id = :user_id";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
        } catch (\Exception $e) {
            error_log("Get patient IDs failed: " . $e->getMessage());
            return [];
        }
    }
}
