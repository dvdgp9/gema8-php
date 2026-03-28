<?php
/**
 * Conversation Message Model
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class ConversationMessage {
    /**
     * Find message by ID
     */
    public static function find(int $id): ?array {
        $stmt = db()->prepare("SELECT * FROM conversation_messages WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Create new message
     */
    public static function create(
        int $conversationId,
        string $direction,
        string $originalText,
        string $translatedText,
        ?string $culturalNote = null
    ): ?array {
        $stmt = db()->prepare(
            "INSERT INTO conversation_messages 
             (conversation_id, direction, original_text, translated_text, cultural_note) 
             VALUES (?, ?, ?, ?, ?)"
        );
        
        if (!$stmt->execute([$conversationId, $direction, $originalText, $translatedText, $culturalNote])) {
            return null;
        }
        
        $insertId = (int) db()->lastInsertId();
        
        // Touch conversation updated_at
        $stmtUpdate = db()->prepare("UPDATE conversations SET updated_at = NOW() WHERE id = ?");
        $stmtUpdate->execute([$conversationId]);
        
        return self::find($insertId);
    }
    
    /**
     * Get messages for a conversation (ordered chronologically)
     */
    public static function getForConversation(int $conversationId, int $limit = 100, int $offset = 0): array {
        $stmt = db()->prepare(
            "SELECT * FROM conversation_messages 
             WHERE conversation_id = ? 
             ORDER BY created_at ASC 
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$conversationId, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get recent messages for context (most recent N messages)
     */
    public static function getRecentForContext(int $conversationId, int $limit = 20): array {
        $stmt = db()->prepare(
            "SELECT * FROM conversation_messages 
             WHERE conversation_id = ? 
             ORDER BY created_at DESC 
             LIMIT ?"
        );
        $stmt->execute([$conversationId, $limit]);
        $messages = $stmt->fetchAll();
        return array_reverse($messages);
    }
    
    /**
     * Count messages in a conversation
     */
    public static function countForConversation(int $conversationId): int {
        $stmt = db()->prepare("SELECT COUNT(*) FROM conversation_messages WHERE conversation_id = ?");
        $stmt->execute([$conversationId]);
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * Delete a specific message (verify ownership via conversation)
     */
    public static function delete(int $id, int $conversationId): bool {
        $stmt = db()->prepare(
            "DELETE FROM conversation_messages WHERE id = ? AND conversation_id = ?"
        );
        return $stmt->execute([$id, $conversationId]) && $stmt->rowCount() > 0;
    }
}
