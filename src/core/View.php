<?php

namespace App\Core;

class View
{
    private string $viewsPath;

    public function __construct(string $viewsPath = null)
    {
        $this->viewsPath = $viewsPath ?? __DIR__ . '/../../views';
    }

    /**
     * Render a view template
     *
     * @param string $template Template path relative to views directory (e.g., 'home/index')
     * @param array $data Data to pass to the view
     * @param bool $return Whether to return the output instead of echoing
     * @return string|void
     */
    public function render(string $template, array $data = [], bool $return = false)
    {
        $templatePath = $this->viewsPath . '/' . $template . '.php';

        if (!file_exists($templatePath)) {
            throw new \Exception("View template not found: {$templatePath}");
        }

        // Extract data array to variables
        extract($data);

        if ($return) {
            ob_start();
            require $templatePath;
            return ob_get_clean();
        }

        require $templatePath;
    }

    /**
     * Escape HTML output to prevent XSS attacks
     *
     * @param string|null $value Value to escape
     * @return string Escaped value
     */
    public static function escape(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Alias for escape() for use in templates
     */
    public static function e(?string $value): string
    {
        return self::escape($value);
    }
}
