<?php
/**
 * Session Management
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class Session {
    private static bool $started = false;
    
    /**
     * Start the session with secure settings
     */
    public static function start(): void {
        if (self::$started) {
            return;
        }
        
        // Configure session
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_httponly', 1);
        
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            ini_set('session.cookie_secure', 1);
        }
        
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        
        session_start();
        self::$started = true;
        
        // Regenerate session ID periodically for security
        if (!isset($_SESSION['_created'])) {
            $_SESSION['_created'] = time();
        } elseif (time() - $_SESSION['_created'] > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['_created'] = time();
        }
    }
    
    /**
     * Destroy the session
     */
    public static function destroy(): void {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        self::$started = false;
    }
    
    /**
     * Regenerate session ID
     */
    public static function regenerate(): void {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
    
    /**
     * Get session value
     */
    public static function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Set session value
     */
    public static function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Check if session key exists
     */
    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove session value
     */
    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }
}
