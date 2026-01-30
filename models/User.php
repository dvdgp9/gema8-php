<?php
/**
 * User Model
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class User {
    /**
     * Find user by ID
     */
    public static function find(int $id): ?array {
        $stmt = db()->prepare("SELECT id, email, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Find user by email
     */
    public static function findByEmail(string $email): ?array {
        $stmt = db()->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Create new user
     */
    public static function create(string $email, string $password): ?int {
        $passwordHash = hashPassword($password);
        
        try {
            db()->beginTransaction();
            
            // Create user
            $stmt = db()->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
            $stmt->execute([$email, $passwordHash]);
            $userId = (int) db()->lastInsertId();
            
            // Create profile with default values
            Profile::create($userId);
            
            db()->commit();
            return $userId;
            
        } catch (PDOException $e) {
            db()->rollBack();
            error_log('User creation error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Verify user credentials
     */
    public static function verifyCredentials(string $email, string $password): ?array {
        $user = self::findByEmail($email);
        
        if (!$user) {
            return null;
        }
        
        if (!verifyPassword($password, $user['password_hash'])) {
            return null;
        }
        
        // Check if password needs rehashing
        if (needsRehash($user['password_hash'])) {
            self::updatePassword($user['id'], $password);
        }
        
        // Remove sensitive data
        unset($user['password_hash'], $user['reset_token'], $user['reset_expires']);
        
        return $user;
    }
    
    /**
     * Update user password
     */
    public static function updatePassword(int $userId, string $newPassword): bool {
        $passwordHash = hashPassword($newPassword);
        
        $stmt = db()->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        return $stmt->execute([$passwordHash, $userId]);
    }
    
    /**
     * Generate password reset token
     */
    public static function createResetToken(string $email): ?string {
        $user = self::findByEmail($email);
        
        if (!$user) {
            return null;
        }
        
        $token = generateToken(32);
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $stmt = db()->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
        $stmt->execute([$token, $expires, $user['id']]);
        
        return $token;
    }
    
    /**
     * Find user by reset token
     */
    public static function findByResetToken(string $token): ?array {
        $stmt = db()->prepare(
            "SELECT id, email FROM users WHERE reset_token = ? AND reset_expires > NOW()"
        );
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Reset password with token
     */
    public static function resetPasswordWithToken(string $token, string $newPassword): bool {
        $user = self::findByResetToken($token);
        
        if (!$user) {
            return false;
        }
        
        return self::updatePassword($user['id'], $newPassword);
    }
    
    /**
     * Delete user account
     */
    public static function delete(int $userId): bool {
        try {
            // Profile and related data will be deleted via CASCADE
            $stmt = db()->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log('User deletion error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if email exists
     */
    public static function emailExists(string $email): bool {
        $stmt = db()->prepare("SELECT 1 FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return (bool) $stmt->fetch();
    }
    
    /**
     * Get all users with their profiles (for admin panel)
     */
    public static function getAllWithProfiles(int $limit = 50, int $offset = 0, string $search = ''): array {
        $sql = "SELECT u.id, u.email, u.created_at, p.role, p.credits, p.current_language 
                FROM users u 
                LEFT JOIN profiles p ON u.id = p.user_id";
        
        $params = [];
        if (!empty($search)) {
            $sql .= " WHERE u.email LIKE ?";
            $params[] = '%' . $search . '%';
        }
        
        $sql .= " ORDER BY u.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Count total users (for pagination)
     */
    public static function count(string $search = ''): int {
        $sql = "SELECT COUNT(*) FROM users";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " WHERE email LIKE ?";
            $params[] = '%' . $search . '%';
        }
        
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * Get user with profile for admin
     */
    public static function findWithProfile(int $id): ?array {
        $stmt = db()->prepare(
            "SELECT u.id, u.email, u.created_at, p.role, p.credits, p.current_language, p.language_progress
             FROM users u 
             INNER JOIN profiles p ON u.id = p.user_id 
             WHERE u.id = ?"
        );
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        if ($user && isset($user['language_progress']) && $user['language_progress']) {
            $user['language_progress'] = json_decode($user['language_progress'], true);
        }
        
        return $user ?: null;
    }
}
