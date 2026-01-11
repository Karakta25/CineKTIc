<?php

use App\Core\View;

/**
 * Escape HTML output
 */
function e(?string $value): string
{
    return View::escape($value);
}

/**
 * Generate URL for a route
 */
function url(string $path = ''): string
{
    $baseUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/');
    $path = ltrim($path, '/');
    return $baseUrl . '/' . $path;
}

/**
 * Generate asset URL
 */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    return url('assets/' . $path);
}

/**
 * Redirect helper
 */
function redirect(string $url, int $statusCode = 302): void
{
    header("Location: {$url}", true, $statusCode);
    exit;
}

/**
 * Get old input value (for form repopulation after validation errors)
 */
function old(string $key, $default = '')
{
    return $_SESSION['old_input'][$key] ?? $default;
}

/**
 * Flash a message to the session
 */
function flash(string $key, $value = null)
{
    if ($value === null) {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    $_SESSION['flash'][$key] = $value;
}

/**
 * Get session value
 */
function session(string $key = null, $default = null)
{
    if ($key === null) {
        return $_SESSION ?? [];
    }

    return $_SESSION[$key] ?? $default;
}

/**
 * Check if user is authenticated
 */
function isAuthenticated(): bool
{
    return isset($_SESSION['user']);
}

/**
 * Get current authenticated user email
 */
function currentUser(): ?string
{
    return $_SESSION['user'] ?? null;
}

/**
 * Generate CSRF token
 */
function csrfToken(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Generate CSRF token field for forms
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

/**
 * Dump and die (for debugging)
 */
function dd(...$vars): void
{
    echo '<pre>';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    die();
}

/**
 * Log message to file
 */
function logMessage(string $message, string $level = 'INFO'): void
{
    $logDir = __DIR__ . '/../../storage/logs';
    $logFile = $logDir . '/' . date('Y-m-d') . '.log';

    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;

    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
