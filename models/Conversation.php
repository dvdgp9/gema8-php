<?php
/**
 * Conversation Model
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class Conversation {
    /**
     * Find conversation by ID
     */
    public static function find(int $id): ?array {
        $stmt = db()->prepare("SELECT * FROM conversations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Find conversation by ID and user (ownership verification)
     */
    public static function findForUser(int $id, int $userId): ?array {
        $stmt = db()->prepare("SELECT * FROM conversations WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Create new conversation
     */
    public static function create(
        int $userId,
        string $title,
        string $targetLanguage,
        string $level = 'intermediate',
        string $tone = 'keep',
        string $fidelity = 'natural'
    ): ?array {
        $stmt = db()->prepare(
            "INSERT INTO conversations 
             (user_id, title, target_language, level, tone, fidelity) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        
        if (!$stmt->execute([$userId, $title, $targetLanguage, $level, $tone, $fidelity])) {
            return null;
        }
        
        return self::find((int) db()->lastInsertId());
    }
    
    /**
     * Update conversation settings
     */
    public static function updateSettings(
        int $id,
        int $userId,
        string $title,
        string $level,
        string $tone,
        string $fidelity
    ): bool {
        $stmt = db()->prepare(
            "UPDATE conversations 
             SET title = ?, level = ?, tone = ?, fidelity = ? 
             WHERE id = ? AND user_id = ?"
        );
        return $stmt->execute([$title, $level, $tone, $fidelity, $id, $userId]);
    }
    
    /**
     * Update conversation summary (for context windowing)
     */
    public static function updateSummary(int $id, string $summary): bool {
        $stmt = db()->prepare("UPDATE conversations SET summary = ? WHERE id = ?");
        return $stmt->execute([$summary, $id]);
    }
    
    /**
     * Archive conversation
     */
    public static function archive(int $id, int $userId): bool {
        $stmt = db()->prepare(
            "UPDATE conversations SET is_archived = 1 WHERE id = ? AND user_id = ?"
        );
        return $stmt->execute([$id, $userId]) && $stmt->rowCount() > 0;
    }
    
    /**
     * Unarchive conversation
     */
    public static function unarchive(int $id, int $userId): bool {
        $stmt = db()->prepare(
            "UPDATE conversations SET is_archived = 0 WHERE id = ? AND user_id = ?"
        );
        return $stmt->execute([$id, $userId]) && $stmt->rowCount() > 0;
    }
    
    /**
     * Get user's active conversations (not archived)
     */
    public static function getForUser(int $userId, int $limit = 50, int $offset = 0): array {
        $stmt = db()->prepare(
            "SELECT c.*, 
                    (SELECT COUNT(*) FROM conversation_messages WHERE conversation_id = c.id) as message_count,
                    (SELECT created_at FROM conversation_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_at
             FROM conversations c
             WHERE c.user_id = ? AND c.is_archived = 0
             ORDER BY COALESCE(
                 (SELECT created_at FROM conversation_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1),
                 c.created_at
             ) DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get user's archived conversations
     */
    public static function getArchivedForUser(int $userId, int $limit = 50, int $offset = 0): array {
        $stmt = db()->prepare(
            "SELECT c.*, 
                    (SELECT COUNT(*) FROM conversation_messages WHERE conversation_id = c.id) as message_count,
                    (SELECT created_at FROM conversation_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_at
             FROM conversations c
             WHERE c.user_id = ? AND c.is_archived = 1
             ORDER BY c.updated_at DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Search conversations by title
     */
    public static function search(int $userId, string $query, int $limit = 20): array {
        $searchTerm = '%' . $query . '%';
        $stmt = db()->prepare(
            "SELECT c.*, 
                    (SELECT COUNT(*) FROM conversation_messages WHERE conversation_id = c.id) as message_count,
                    (SELECT created_at FROM conversation_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_at
             FROM conversations c
             WHERE c.user_id = ? AND c.title LIKE ?
             ORDER BY c.updated_at DESC
             LIMIT ?"
        );
        $stmt->execute([$userId, $searchTerm, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Count user's active conversations
     */
    public static function countForUser(int $userId): int {
        $stmt = db()->prepare("SELECT COUNT(*) FROM conversations WHERE user_id = ? AND is_archived = 0");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * Delete conversation and all its messages (CASCADE)
     */
    public static function delete(int $id, int $userId): bool {
        $stmt = db()->prepare("DELETE FROM conversations WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]) && $stmt->rowCount() > 0;
    }
    
    /**
     * Delete all conversations for user
     */
    public static function deleteAllForUser(int $userId): bool {
        $stmt = db()->prepare("DELETE FROM conversations WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
