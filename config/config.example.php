<?php
/**
 * Gema8 - Configuration File
 * 
 * INSTRUCTIONS:
 * 1. Copy this file to config.php
 * 2. Update the values below with your configuration
 * 3. Never commit config.php to version control
 */

// Prevent direct access
if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

// ===========================================
// ENVIRONMENT
// ===========================================
// Set to 'production' for live site
define('ENV', 'development');

// ===========================================
// BASE URL
// ===========================================
// Your site URL without trailing slash
// Examples:
//   - Local: 'http://localhost/gema8'
//   - Subdomain: 'https://gema8.yourdomain.com'
//   - Subfolder: 'https://yourdomain.com/gema8'
define('BASE_URL', 'http://localhost/gema8');

// ===========================================
// DATABASE
// ===========================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'gema8');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ===========================================
// API KEYS
// ===========================================
// Get your key from: https://aistudio.google.com/
define('GEMINI_API_KEY', 'YOUR_GEMINI_API_KEY');

// ===========================================
// SESSION
// ===========================================
define('SESSION_NAME', 'gema8_session');
define('SESSION_LIFETIME', 86400 * 7); // 7 days

// ===========================================
// SECURITY
// ===========================================
define('HASH_COST', 12); // bcrypt cost factor (10-12 recommended)

// ===========================================
// CREDIT SYSTEM
// ===========================================
define('CREDIT_COST_TRANSLATE', 1);
define('CREDIT_COST_ASK', 1);
define('CREDIT_COST_WHISPER', 1);
define('DEFAULT_CREDITS', 500); // Credits for new users

// ===========================================
// USER ROLES
// ===========================================
define('ROLE_WHISPER', 'Whisper');  // Basic user
define('ROLE_VOICE', 'Voice');      // Premium user
define('ROLE_ORACLE', 'Oracle');    // Admin (unlimited credits)

// ===========================================
// SUPPORTED LANGUAGES
// ===========================================
$SUPPORTED_LANGUAGES = [
    'indonesian' => 'Indonesian',
    'vietnamese' => 'Vietnamese',
    'french' => 'French',
    'spanish' => 'Spanish',
    'portuguese' => 'Portuguese',
    'italian' => 'Italian',
    'german' => 'German',
    'dutch' => 'Dutch',
    'swedish' => 'Swedish',
    'norwegian' => 'Norwegian',
    'danish' => 'Danish',
    'finnish' => 'Finnish',
    'russian' => 'Russian',
    'polish' => 'Polish',
    'czech' => 'Czech',
    'hungarian' => 'Hungarian',
    'romanian' => 'Romanian',
    'bulgarian' => 'Bulgarian',
    'japanese' => 'Japanese',
    'korean' => 'Korean',
    'chinese' => 'Chinese (Mandarin)',
    'thai' => 'Thai',
    'hindi' => 'Hindi',
    'arabic' => 'Arabic',
    'hebrew' => 'Hebrew',
    'turkish' => 'Turkish',
    'greek' => 'Greek',
    'ukrainian' => 'Ukrainian',
    'croatian' => 'Croatian',
    'serbian' => 'Serbian'
];

// ===========================================
// ERROR REPORTING
// ===========================================
if (ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ===========================================
// TIMEZONE
// ===========================================
date_default_timezone_set('UTC');
