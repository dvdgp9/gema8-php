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
