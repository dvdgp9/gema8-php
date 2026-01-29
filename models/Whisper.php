<?php
/**
 * Whisper Model (Situational Phrases)
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class Whisper {
    /**
     * Find whisper by ID
     */
    public static function find(int $id): ?array {
        $stmt = db()->prepare("SELECT * FROM whispers WHERE id = ?");
        $stmt->execute([$id]);
        $whisper = $stmt->fetch();
        
        if ($whisper && $whisper['phrases']) {
            $whisper['phrases'] = json_decode($whisper['phrases'], true);
        }
        
        return $whisper ?: null;
    }
    
    /**
     * Find whisper by ID and user (for ownership verification)
     */
    public static function findForUser(int $id, int $userId): ?array {
        $stmt = db()->prepare("SELECT * FROM whispers WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $whisper = $stmt->fetch();
        
        if ($whisper && $whisper['phrases']) {
            $whisper['phrases'] = json_decode($whisper['phrases'], true);
        }
        
        return $whisper ?: null;
    }
    
    /**
     * Create new whisper
     */
    public static function create(
        int $userId,
        string $title,
        string $situationContext,
        string $targetLanguage,
        array $phrases
    ): ?array {
        $phrasesJson = json_encode($phrases);
        $phraseCount = count($phrases);
        
        $stmt = db()->prepare(
            "INSERT INTO whispers 
             (user_id, title, situation_context, target_language, phrases, phrase_count) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        
        if (!$stmt->execute([$userId, $title, $situationContext, $targetLanguage, $phrasesJson, $phraseCount])) {
            return null;
        }
        
        return self::find((int) db()->lastInsertId());
    }
    
    /**
     * Get user's whispers
     */
    public static function getForUser(int $userId, int $limit = 20, int $offset = 0): array {
        $stmt = db()->prepare(
            "SELECT * FROM whispers 
             WHERE user_id = ? 
             ORDER BY created_at DESC 
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$userId, $limit, $offset]);
        $whispers = $stmt->fetchAll();
        
        // Decode phrases JSON for each whisper
        foreach ($whispers as &$whisper) {
            if ($whisper['phrases']) {
                $whisper['phrases'] = json_decode($whisper['phrases'], true);
            }
        }
        
        return $whispers;
    }
    
    /**
     * Get user's whispers filtered by language
     */
    public static function getForUserByLanguage(
        int $userId, 
        string $targetLanguage, 
        int $limit = 20, 
        int $offset = 0
    ): array {
        $stmt = db()->prepare(
            "SELECT * FROM whispers 
             WHERE user_id = ? AND target_language = ?
             ORDER BY created_at DESC 
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$userId, $targetLanguage, $limit, $offset]);
        $whispers = $stmt->fetchAll();
        
        foreach ($whispers as &$whisper) {
            if ($whisper['phrases']) {
                $whisper['phrases'] = json_decode($whisper['phrases'], true);
            }
        }
        
        return $whispers;
    }
    
    /**
     * Count user's whispers
     */
    public static function countForUser(int $userId): int {
        $stmt = db()->prepare("SELECT COUNT(*) FROM whispers WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * Delete whisper
     */
    public static function delete(int $id, int $userId): bool {
        $stmt = db()->prepare("DELETE FROM whispers WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]) && $stmt->rowCount() > 0;
    }
    
    /**
     * Delete all whispers for user
     */
    public static function deleteAllForUser(int $userId): bool {
        $stmt = db()->prepare("DELETE FROM whispers WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
