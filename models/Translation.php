<?php
/**
 * Translation Model
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class Translation {
    /**
     * Find translation by ID
     */
    public static function find(int $id): ?array {
        $stmt = db()->prepare("SELECT * FROM translations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Find existing translation for user
     */
    public static function findForUser(
        int $userId, 
        string $normalizedText, 
        string $sourceLanguage, 
        string $targetLanguage
    ): ?array {
        $stmt = db()->prepare(
            "SELECT * FROM translations 
             WHERE user_id = ? AND normalized_text = ? 
             AND source_language = ? AND target_language = ?"
        );
        $stmt->execute([$userId, $normalizedText, $sourceLanguage, $targetLanguage]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Create new translation
     */
    public static function create(
        int $userId,
        string $originalText,
        string $translatedText,
        string $sourceLanguage,
        string $targetLanguage
    ): ?array {
        $normalizedText = normalizeText($originalText);
        
        $stmt = db()->prepare(
            "INSERT INTO translations 
             (user_id, original_text, normalized_text, translated_text, source_language, target_language, count) 
             VALUES (?, ?, ?, ?, ?, ?, 1)"
        );
        
        if (!$stmt->execute([$userId, $originalText, $normalizedText, $translatedText, $sourceLanguage, $targetLanguage])) {
            return null;
        }
        
        return self::find((int) db()->lastInsertId());
    }
    
    /**
     * Save or update translation (increment count if exists)
     */
    public static function saveOrUpdate(
        int $userId,
        string $originalText,
        string $translatedText,
        string $sourceLanguage,
        string $targetLanguage
    ): ?array {
        $normalizedText = normalizeText($originalText);
        
        // Check if translation already exists
        $existing = self::findForUser($userId, $normalizedText, $sourceLanguage, $targetLanguage);
        
        if ($existing) {
            // Update count
            $stmt = db()->prepare(
                "UPDATE translations SET count = count + 1, updated_at = NOW() WHERE id = ?"
            );
            $stmt->execute([$existing['id']]);
            
            // Return updated record
            return self::find($existing['id']);
        }
        
        // Create new
        return self::create($userId, $originalText, $translatedText, $sourceLanguage, $targetLanguage);
    }
    
    /**
     * Get user's translation history (echoes)
     */
    public static function getHistory(int $userId, int $limit = 50, int $offset = 0): array {
        $stmt = db()->prepare(
            "SELECT * FROM translations 
             WHERE user_id = ? 
             ORDER BY updated_at DESC 
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get user's translation history filtered by language
     */
    public static function getHistoryByLanguage(
        int $userId, 
        string $targetLanguage, 
        int $limit = 50, 
        int $offset = 0
    ): array {
        $stmt = db()->prepare(
            "SELECT * FROM translations 
             WHERE user_id = ? AND target_language = ?
             ORDER BY updated_at DESC 
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$userId, $targetLanguage, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Count user's translations
     */
    public static function countForUser(int $userId): int {
        $stmt = db()->prepare("SELECT COUNT(*) FROM translations WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * Delete translation
     */
    public static function delete(int $id, int $userId): bool {
        $stmt = db()->prepare("DELETE FROM translations WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]) && $stmt->rowCount() > 0;
    }
    
    /**
     * Delete all translations for user
     */
    public static function deleteAllForUser(int $userId): bool {
        $stmt = db()->prepare("DELETE FROM translations WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
    
    /**
     * Search translations
     */
    public static function search(int $userId, string $query, int $limit = 20): array {
        $searchTerm = '%' . $query . '%';
        $stmt = db()->prepare(
            "SELECT * FROM translations 
             WHERE user_id = ? AND (original_text LIKE ? OR translated_text LIKE ?)
             ORDER BY updated_at DESC 
             LIMIT ?"
        );
        $stmt->execute([$userId, $searchTerm, $searchTerm, $limit]);
        return $stmt->fetchAll();
    }
}
