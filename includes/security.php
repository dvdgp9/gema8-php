<?php
/**
 * Security Functions
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

/**
 * Generate CSRF token
 */
function csrfToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateToken(32);
    }
    return $_SESSION['csrf_token'];
}

/**
 * Generate CSRF input field
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

/**
 * Verify CSRF token
 */
function verifyCsrf(): bool {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    
    if (empty($token) || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Require valid CSRF token or die
 */
function requireCsrf(): void {
    if (!verifyCsrf()) {
        if (isAjax()) {
            jsonResponse(['error' => 'Invalid security token'], 403);
        }
        http_response_code(403);
        die('Invalid security token. Please refresh the page and try again.');
    }
}

/**
 * Regenerate CSRF token
 */
function regenerateCsrf(): void {
    $_SESSION['csrf_token'] = generateToken(32);
}

/**
 * Sanitize input string
 */
function sanitize(string $input): string {
    return trim(strip_tags($input));
}

/**
 * Sanitize email
 */
function sanitizeEmail(string $email): string {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

/**
 * Validate email format
 */
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Hash password securely
 */
function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
}

/**
 * Verify password against hash
 */
function verifyPassword(string $password, string $hash): bool {
    return password_verify($password, $hash);
}

/**
 * Check if password needs rehashing
 */
function needsRehash(string $hash): bool {
    return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
}

/**
 * Rate limiting helper
 */
function checkRateLimit(string $key, int $maxAttempts = 5, int $decayMinutes = 15): bool {
    $cacheKey = 'rate_limit_' . md5($key);
    
    if (!isset($_SESSION[$cacheKey])) {
        $_SESSION[$cacheKey] = ['attempts' => 0, 'expires' => time() + ($decayMinutes * 60)];
    }
    
    // Reset if expired
    if ($_SESSION[$cacheKey]['expires'] < time()) {
        $_SESSION[$cacheKey] = ['attempts' => 0, 'expires' => time() + ($decayMinutes * 60)];
    }
    
    $_SESSION[$cacheKey]['attempts']++;
    
    return $_SESSION[$cacheKey]['attempts'] <= $maxAttempts;
}

/**
 * Get remaining rate limit attempts
 */
function getRateLimitRemaining(string $key, int $maxAttempts = 5): int {
    $cacheKey = 'rate_limit_' . md5($key);
    
    if (!isset($_SESSION[$cacheKey]) || $_SESSION[$cacheKey]['expires'] < time()) {
        return $maxAttempts;
    }
    
    return max(0, $maxAttempts - $_SESSION[$cacheKey]['attempts']);
}

/**
 * Clear rate limit
 */
function clearRateLimit(string $key): void {
    $cacheKey = 'rate_limit_' . md5($key);
    unset($_SESSION[$cacheKey]);
}
