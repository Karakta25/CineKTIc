<?php

namespace App\Core;

abstract class Controller
{
    protected View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    /**
     * Render a view template
     *
     * @param string $template Template path (e.g., 'home/index')
     * @param array $data Data to pass to the view
     */
    protected function render(string $template, array $data = []): void
    {
        $this->view->render($template, $data);
    }

    /**
     * Redirect to another URL
     *
     * @param string $url URL to redirect to
     * @param int $statusCode HTTP status code (default 302)
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        header("Location: {$url}", true, $statusCode);
        exit;
    }

    /**
     * Send JSON response
     *
     * @param mixed $data Data to encode as JSON
     * @param int $statusCode HTTP status code
     */
    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Get POST data
     *
     * @param string|null $key Specific key to retrieve
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    protected function post(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }

        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET data
     *
     * @param string|null $key Specific key to retrieve
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    protected function get(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }

        return $_GET[$key] ?? $default;
    }

    /**
     * Check if request is POST
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if request is GET
     */
    protected function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrfToken(): bool
    {
        $token = $this->post('csrf_token');
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
