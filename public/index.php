<?php
/**
 * Gema8 - Main Entry Point
 * All requests are routed through this file
 */

define('GEMA8', true);
define('ROOT_PATH', dirname(__DIR__));

// Load configuration
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';

// Load helpers and includes
require_once ROOT_PATH . '/includes/helpers.php';
require_once ROOT_PATH . '/includes/security.php';
require_once ROOT_PATH . '/includes/session.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/gemini.php';

// Load models
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/models/Profile.php';
require_once ROOT_PATH . '/models/Translation.php';
require_once ROOT_PATH . '/models/Whisper.php';
require_once ROOT_PATH . '/models/Tip.php';

// Initialize session
Session::start();

// Check remember token for persistent login
checkRememberToken();

// Get current route
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace(rtrim(parse_url(BASE_URL, PHP_URL_PATH) ?: '', '/'), '', $uri);
$uri = $uri === '' ? '/' : $uri;

// Define routes
$routes = [
    // Public routes
    'GET' => [
        '/' => 'DashboardController@index',
        '/auth' => 'AuthController@showAuth',
        '/auth/login' => 'AuthController@showLogin',
        '/auth/register' => 'AuthController@showRegister',
        '/auth/forgot-password' => 'AuthController@showForgotPassword',
        '/auth/reset-password' => 'AuthController@showResetPassword',
        '/logout' => 'AuthController@logout',
        '/history' => 'HistoryController@index',
        '/whispers' => 'WhisperController@index',
        '/account' => 'AccountController@index',
        '/admin' => 'AdminController@index',
        '/admin/user' => 'AdminController@editUser',
    ],
    'POST' => [
        '/auth/login' => 'AuthController@login',
        '/auth/register' => 'AuthController@register',
        '/auth/forgot-password' => 'AuthController@forgotPassword',
        '/auth/reset-password' => 'AuthController@resetPassword',
        '/account/delete' => 'AccountController@delete',
        '/account/update-language' => 'AccountController@updateLanguage',
        
        // API endpoints
        '/api/translate' => 'ApiController@translate',
        '/api/ask-question' => 'ApiController@askQuestion',
        '/api/generate-whisper' => 'ApiController@generateWhisper',
        '/api/generate-tip' => 'ApiController@generateTip',
        '/api/delete-translation' => 'ApiController@deleteTranslation',
        '/api/delete-whisper' => 'ApiController@deleteWhisper',
        
        // Admin endpoints
        '/admin/user/update' => 'AdminController@updateUser',
        '/admin/user/delete' => 'AdminController@deleteUser',
        '/admin/add-credits' => 'AdminController@addCredits',
    ]
];

// Load controllers
require_once ROOT_PATH . '/controllers/Controller.php';
require_once ROOT_PATH . '/controllers/AuthController.php';
require_once ROOT_PATH . '/controllers/DashboardController.php';
require_once ROOT_PATH . '/controllers/HistoryController.php';
require_once ROOT_PATH . '/controllers/WhisperController.php';
require_once ROOT_PATH . '/controllers/AccountController.php';
require_once ROOT_PATH . '/controllers/ApiController.php';
require_once ROOT_PATH . '/controllers/AdminController.php';

// Route the request
$method = $_SERVER['REQUEST_METHOD'];

if (isset($routes[$method][$uri])) {
    $handler = $routes[$method][$uri];
    [$controller, $action] = explode('@', $handler);
    
    $controllerInstance = new $controller();
    $controllerInstance->$action();
} else {
    // 404 Not Found
    http_response_code(404);
    require_once ROOT_PATH . '/views/errors/404.php';
}
