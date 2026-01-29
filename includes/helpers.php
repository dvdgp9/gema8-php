<?php
/**
 * Helper Functions
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

/**
 * Redirect to a URL
 */
function redirect(string $url): void {
    if (strpos($url, 'http') !== 0) {
        $url = BASE_URL . $url;
    }
    header("Location: $url");
    exit;
}

/**
 * Get asset URL
 */
function asset(string $path): string {
    return BASE_URL . '/public/' . ltrim($path, '/');
}

/**
 * Escape HTML output
 */
function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Get old input value (for form repopulation)
 */
function old(string $key, string $default = ''): string {
    return $_SESSION['old_input'][$key] ?? $default;
}

/**
 * Set old input values
 */
function setOldInput(array $input): void {
    $_SESSION['old_input'] = $input;
}

/**
 * Clear old input values
 */
function clearOldInput(): void {
    unset($_SESSION['old_input']);
}

/**
 * Flash message functions
 */
function flash(string $type, string $message): void {
    $_SESSION['flash'][$type] = $message;
}

function getFlash(string $type): ?string {
    $message = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $message;
}

function hasFlash(string $type): bool {
    return isset($_SESSION['flash'][$type]);
}

/**
 * Normalize text for comparison
 */
function normalizeText(string $text): string {
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = preg_replace('/\s+/', ' ', $text);
    return $text;
}

/**
 * Generate a random token
 */
function generateToken(int $length = 32): string {
    return bin2hex(random_bytes($length));
}

/**
 * Format date for display
 */
function formatDate(string $date, string $format = 'd M Y'): string {
    return date($format, strtotime($date));
}

/**
 * Get relative time (e.g., "2 hours ago")
 */
function timeAgo(string $datetime): string {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    
    return formatDate($datetime);
}

/**
 * JSON response helper
 */
function jsonResponse(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Check if request is AJAX
 */
function isAjax(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get current user's timezone
 */
function getUserTimezone(): string {
    return $_SESSION['user_timezone'] ?? 'UTC';
}

/**
 * Truncate text with ellipsis
 */
function truncate(string $text, int $length = 100): string {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . '...';
}

/**
 * Get language display name
 */
function getLanguageName(string $code): string {
    global $SUPPORTED_LANGUAGES;
    if ($code === 'english') return 'English';
    return $SUPPORTED_LANGUAGES[$code] ?? ucfirst($code);
}

/**
 * Get short language code
 */
function getLanguageShortName(string $code): string {
    if ($code === 'english') return 'EN';
    $name = getLanguageName($code);
    return mb_strtoupper(mb_substr($name, 0, 2), 'UTF-8');/**/
}

/**
 * Check if language is supported
 */
function isValidLanguage(string $code): bool {
    global $SUPPORTED_LANGUAGES;
    return isset($SUPPORTED_LANGUAGES[$code]) || $code === 'english';
}
