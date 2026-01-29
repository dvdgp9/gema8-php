<?php
/**
 * Tip Model (User Generated Tips for anti-repetition)
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class Tip {
    /**
     * Get today's tip for user and language
     */
    public static function getTodaysTip(int $userId, string $language): ?string {
        $today = date('Y-m-d');
        
        $stmt = db()->prepare(
            "SELECT tip_content FROM user_generated_tips 
             WHERE user_id = ? AND language = ? AND DATE(created_at) = ?
             ORDER BY created_at DESC 
             LIMIT 1"
        );
        $stmt->execute([$userId, $language, $today]);
        $result = $stmt->fetch();
        
        return $result ? $result['tip_content'] : null;
    }
    
    /**
     * Store generated tip
     */
    public static function store(int $userId, string $language, string $tipContent, string $briefSummary): bool {
        $stmt = db()->prepare(
            "INSERT INTO user_generated_tips (user_id, language, tip_content, brief_summary) 
             VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$userId, $language, $tipContent, $briefSummary]);
    }
    
    /**
     * Get recent tip summaries for anti-repetition (last 30 days)
     */
    public static function getRecentSummaries(int $userId, string $language, int $days = 30): array {
        $stmt = db()->prepare(
            "SELECT brief_summary FROM user_generated_tips 
             WHERE user_id = ? AND language = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? DAY)
             ORDER BY created_at DESC"
        );
        $stmt->execute([$userId, $language, $days]);
        $results = $stmt->fetchAll();
        
        return array_column($results, 'brief_summary');
    }
    
    /**
     * Get user's tip history
     */
    public static function getHistory(int $userId, string $language = null, int $limit = 20): array {
        if ($language) {
            $stmt = db()->prepare(
                "SELECT * FROM user_generated_tips 
                 WHERE user_id = ? AND language = ?
                 ORDER BY created_at DESC 
                 LIMIT ?"
            );
            $stmt->execute([$userId, $language, $limit]);
        } else {
            $stmt = db()->prepare(
                "SELECT * FROM user_generated_tips 
                 WHERE user_id = ?
                 ORDER BY created_at DESC 
                 LIMIT ?"
            );
            $stmt->execute([$userId, $limit]);
        }
        
        return $stmt->fetchAll();
    }
    
    /**
     * Count tips for user
     */
    public static function countForUser(int $userId): int {
        $stmt = db()->prepare("SELECT COUNT(*) FROM user_generated_tips WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * Delete old tips (cleanup, keep last 90 days)
     */
    public static function cleanupOld(int $daysToKeep = 90): int {
        $stmt = db()->prepare(
            "DELETE FROM user_generated_tips WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)"
        );
        $stmt->execute([$daysToKeep]);
        return $stmt->rowCount();
    }
}
