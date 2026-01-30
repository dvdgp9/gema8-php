<?php
/**
 * Profile Model
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class Profile {
    /**
     * Find profile by user ID
     */
    public static function findByUserId(int $userId): ?array {
        $stmt = db()->prepare("SELECT * FROM profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch();
        
        if ($profile && $profile['language_progress']) {
            $profile['language_progress'] = json_decode($profile['language_progress'], true);
        }
        
        return $profile ?: null;
    }
    
    /**
     * Create profile for new user
     */
    public static function create(int $userId, string $role = 'Whisper', int $credits = null): bool {
        $credits = $credits ?? DEFAULT_CREDITS;
        $defaultProgress = json_encode([]);
        
        $stmt = db()->prepare(
            "INSERT INTO profiles (user_id, role, credits, current_language, language_progress) 
             VALUES (?, ?, ?, 'indonesian', ?)"
        );
        return $stmt->execute([$userId, $role, $credits, $defaultProgress]);
    }
    
    /**
     * Update current language and track progress
     */
    public static function updateLanguage(int $userId, string $language): bool {
        $profile = self::findByUserId($userId);
        
        if (!$profile) {
            return false;
        }
        
        $progress = $profile['language_progress'] ?? [];
        $today = date('Y-m-d');
        
        // Update progress for this language
        if (!isset($progress[$language])) {
            $progress[$language] = [
                'days_active' => 1,
                'last_active' => $today
            ];
        } else {
            // Only increment days_active if last_active was a different day
            if ($progress[$language]['last_active'] !== $today) {
                $progress[$language]['days_active']++;
                $progress[$language]['last_active'] = $today;
            }
        }
        
        $stmt = db()->prepare(
            "UPDATE profiles SET current_language = ?, language_progress = ?, updated_at = NOW() 
             WHERE user_id = ?"
        );
        return $stmt->execute([$language, json_encode($progress), $userId]);
    }
    
    /**
     * Get language progress for specific language
     */
    public static function getLanguageProgress(int $userId, string $language): ?array {
        $profile = self::findByUserId($userId);
        
        if (!$profile) {
            return null;
        }
        
        $progress = $profile['language_progress'] ?? [];
        
        return $progress[$language] ?? [
            'days_active' => 0,
            'last_active' => null
        ];
    }
    
    /**
     * Decrement user credits
     */
    public static function decrementCredits(int $userId, int $amount = 1): bool {
        $profile = self::findByUserId($userId);
        
        if (!$profile) {
            return false;
        }
        
        // Oracles have unlimited credits
        if ($profile['role'] === ROLE_ORACLE) {
            return true;
        }
        
        // Check if user has enough credits
        if ($profile['credits'] < $amount) {
            return false;
        }
        
        $stmt = db()->prepare(
            "UPDATE profiles SET credits = credits - ?, updated_at = NOW() 
             WHERE user_id = ? AND credits >= ?"
        );
        $stmt->execute([$amount, $userId, $amount]);
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Add credits to user
     */
    public static function addCredits(int $userId, int $amount): bool {
        $stmt = db()->prepare(
            "UPDATE profiles SET credits = credits + ?, updated_at = NOW() WHERE user_id = ?"
        );
        return $stmt->execute([$amount, $userId]);
    }
    
    /**
     * Update user role
     */
    public static function updateRole(int $userId, string $role): bool {
        if (!in_array($role, [ROLE_WHISPER, ROLE_VOICE, ROLE_ORACLE])) {
            return false;
        }
        
        $stmt = db()->prepare("UPDATE profiles SET role = ?, updated_at = NOW() WHERE user_id = ?");
        return $stmt->execute([$role, $userId]);
    }
    
    /**
     * Check if user has enough credits
     */
    public static function hasCredits(int $userId, int $amount = 1): bool {
        $profile = self::findByUserId($userId);
        
        if (!$profile) {
            return false;
        }
        
        // Oracles have unlimited credits
        if ($profile['role'] === ROLE_ORACLE) {
            return true;
        }
        
        return $profile['credits'] >= $amount;
    }
    
    /**
     * Set credits to a specific amount (admin function)
     */
    public static function setCredits(int $userId, int $credits): bool {
        $stmt = db()->prepare(
            "UPDATE profiles SET credits = ?, updated_at = NOW() WHERE user_id = ?"
        );
        return $stmt->execute([$credits, $userId]);
    }
    
    /**
     * Get statistics for admin dashboard
     */
    public static function getStats(): array {
        $stats = [];
        
        // Total users
        $stmt = db()->query("SELECT COUNT(*) FROM users");
        $stats['total_users'] = (int) $stmt->fetchColumn();
        
        // Users by role
        $stmt = db()->query("SELECT role, COUNT(*) as count FROM profiles GROUP BY role");
        $stats['by_role'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Total credits in circulation
        $stmt = db()->query("SELECT SUM(credits) FROM profiles");
        $stats['total_credits'] = (int) $stmt->fetchColumn();
        
        // New users today
        $stmt = db()->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()");
        $stats['new_today'] = (int) $stmt->fetchColumn();
        
        // New users this week
        $stmt = db()->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $stats['new_week'] = (int) $stmt->fetchColumn();
        
        return $stats;
    }
}
