<?php

/**
 * Front Controller - the main entry point for all requests
 */

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load Composer autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Load helper functions
require_once BASE_PATH . '/src/Helpers/functions.php';

// Start session with secure settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting based on environment
if ($_ENV['APP_DEBUG'] === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set('UTC');

// Create router instance
$router = new App\Core\Router();

// Register middleware
$router->registerMiddleware('auth', App\Middleware\AuthMiddleware::class);

// Load routes
require_once BASE_PATH . '/config/routes.php';

// Get current URI and request method
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Dispatch the request
try {
    $router->dispatch($uri, $method);
} catch (Exception $e) {
    // Log error
    logMessage($e->getMessage(), 'ERROR');

    // Show error page
    if ($_ENV['APP_DEBUG'] === 'true') {
        echo '<h1>Application Error</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        http_response_code(500);
        echo '<h1>500 - Internal Server Error</h1>';
        echo '<p>An error occurred. Please try again later.</p>';
    }
}
