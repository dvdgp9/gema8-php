<?php
/**
 * Authentication Helper Functions
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

/**
 * Get current user ID
 */
function userId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user data
 */
function currentUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }
    
    // Cache user data in session to avoid repeated DB queries
    if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['id'] !== $_SESSION['user_id']) {
        $user = User::find($_SESSION['user_id']);
        if ($user) {
            $_SESSION['user_data'] = $user;
        } else {
            // User no longer exists, log out
            logout();
            return null;
        }
    }
    
    return $_SESSION['user_data'];
}

/**
 * Get current user's profile
 */
function currentProfile(): ?array {
    if (!isLoggedIn()) {
        return null;
    }
    
    // Cache profile data, refresh every 5 minutes
    $cacheKey = 'profile_data';
    $cacheTime = 'profile_cached_at';
    
    if (!isset($_SESSION[$cacheKey]) || 
        !isset($_SESSION[$cacheTime]) || 
        time() - $_SESSION[$cacheTime] > 300) {
        
        $profile = Profile::findByUserId($_SESSION['user_id']);
        if ($profile) {
            $_SESSION[$cacheKey] = $profile;
            $_SESSION[$cacheTime] = time();
        }
    }
    
    return $_SESSION[$cacheKey] ?? null;
}

/**
 * Refresh cached profile data
 */
function refreshProfile(): void {
    if (isLoggedIn()) {
        $profile = Profile::findByUserId($_SESSION['user_id']);
        if ($profile) {
            $_SESSION['profile_data'] = $profile;
            $_SESSION['profile_cached_at'] = time();
        }
    }
}

/**
 * Log in a user
 */
function login(int $userId): void {
    Session::regenerate();
    $_SESSION['user_id'] = $userId;
    unset($_SESSION['user_data'], $_SESSION['profile_data'], $_SESSION['profile_cached_at']);
}

/**
 * Log out current user
 */
function logout(): void {
    Session::destroy();
}

/**
 * Require authentication (redirect to login if not authenticated)
 */
function requireAuth(): void {
    if (!isLoggedIn()) {
        if (isAjax()) {
            jsonResponse(['error' => 'Authentication required'], 401);
        }
        flash('error', 'Please log in to continue.');
        redirect('/auth');
    }
}

/**
 * Require guest (redirect to dashboard if authenticated)
 */
function requireGuest(): void {
    if (isLoggedIn()) {
        redirect('/');
    }
}

/**
 * Check if user has specific role
 */
function hasRole(string $role): bool {
    $profile = currentProfile();
    return $profile && $profile['role'] === $role;
}

/**
 * Check if user is Oracle (admin)
 */
function isOracle(): bool {
    return hasRole(ROLE_ORACLE);
}

/**
 * Check if user has enough credits
 */
function hasCredits(int $amount = 1): bool {
    $profile = currentProfile();
    
    // Oracles have unlimited credits
    if ($profile && $profile['role'] === ROLE_ORACLE) {
        return true;
    }
    
    return $profile && $profile['credits'] >= $amount;
}

/**
 * Deduct credits from user
 */
function deductCredits(int $amount = 1): bool {
    if (!isLoggedIn()) {
        return false;
    }
    
    $profile = currentProfile();
    
    // Oracles don't lose credits
    if ($profile && $profile['role'] === ROLE_ORACLE) {
        return true;
    }
    
    $success = Profile::decrementCredits($_SESSION['user_id'], $amount);
    
    if ($success) {
        refreshProfile();
    }
    
    return $success;
}

/**
 * Create a persistent "remember me" token (60 days)
 */
function createRememberToken(int $userId): void {
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+60 days'));
    
    // Store hashed token in database
    $stmt = db()->prepare(
        "INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)"
    );
    $stmt->execute([$userId, $tokenHash, $expiresAt]);
    
    // Set cookie with plain token (60 days)
    $cookieExpiry = time() + (60 * 24 * 60 * 60);
    setcookie(
        'remember_token',
        $userId . ':' . $token,
        [
            'expires' => $cookieExpiry,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );
}

/**
 * Check and validate remember token from cookie
 */
function checkRememberToken(): bool {
    if (isLoggedIn()) {
        return true;
    }
    
    if (!isset($_COOKIE['remember_token'])) {
        return false;
    }
    
    $parts = explode(':', $_COOKIE['remember_token'], 2);
    if (count($parts) !== 2) {
        clearRememberToken();
        return false;
    }
    
    [$userId, $token] = $parts;
    $userId = (int) $userId;
    $tokenHash = hash('sha256', $token);
    
    // Find valid token
    $stmt = db()->prepare(
        "SELECT id FROM remember_tokens 
         WHERE user_id = ? AND token_hash = ? AND expires_at > NOW()"
    );
    $stmt->execute([$userId, $tokenHash]);
    $result = $stmt->fetch();
    
    if (!$result) {
        clearRememberToken();
        return false;
    }
    
    // Verify user still exists
    $user = User::find($userId);
    if (!$user) {
        clearRememberToken();
        return false;
    }
    
    // Log the user in
    login($userId);
    
    // Rotate the token for security
    deleteRememberToken($userId, $tokenHash);
    createRememberToken($userId);
    
    return true;
}

/**
 * Delete a specific remember token
 */
function deleteRememberToken(int $userId, string $tokenHash): void {
    $stmt = db()->prepare("DELETE FROM remember_tokens WHERE user_id = ? AND token_hash = ?");
    $stmt->execute([$userId, $tokenHash]);
}

/**
 * Clear all remember tokens for a user
 */
function clearAllRememberTokens(int $userId): void {
    $stmt = db()->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
    $stmt->execute([$userId]);
}

/**
 * Clear remember token cookie
 */
function clearRememberToken(): void {
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
}

/**
 * Cleanup expired remember tokens (call periodically)
 */
function cleanupExpiredTokens(): void {
    db()->exec("DELETE FROM remember_tokens WHERE expires_at < NOW()");
}

/**
 * Require Oracle role (superadmin)
 */
function requireOracle(): void {
    requireAuth();
    
    if (!isOracle()) {
        if (isAjax()) {
            jsonResponse(['error' => 'Access denied'], 403);
        }
        flash('error', 'Access denied. Oracle privileges required.');
        redirect('/');
    }
}
