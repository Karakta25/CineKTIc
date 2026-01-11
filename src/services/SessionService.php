<?php

namespace App\Services;

class SessionService
{
    /**
     * Start the session if not already started
     */
    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set a session value
     *
     * @param string $key Session key
     * @param mixed $value Value to store
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value
     *
     * @param string $key Session key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed Session value or default
     */
    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if session key exists
     *
     * @param string $key Session key
     * @return bool True if key exists
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session value
     *
     * @param string $key Session key
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy the entire session
     */
    public function destroy(): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    /**
     * Regenerate session ID (prevents session fixation attacks)
     */
    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    /**
     * Flash a message (will be available only once)
     *
     * @param string $key Flash message key
     * @param mixed $value Message value
     */
    public function flash(string $key, $value): void
    {
        $_SESSION['flash'][$key] = $value;
    }

    /**
     * Get and remove a flash message
     *
     * @param string $key Flash message key
     * @return mixed Flash message or null
     */
    public function getFlash(string $key)
    {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    /**
     * Check if user is authenticated
     *
     * @return bool True if user is logged in
     */
    public function isAuthenticated(): bool
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }

    /**
     * Set authenticated user
     *
     * @param string $email User email
     */
    public function setUser(string $email): void
    {
        $this->set('user', $email);
    }

    /**
     * Get current authenticated user email
     *
     * @return string|null User email or null
     */
    public function getUser(): ?string
    {
        return $this->get('user');
    }

    /**
     * Store old input (for form repopulation after validation errors)
     *
     * @param array $input Input data
     */
    public function setOldInput(array $input): void
    {
        $_SESSION['old_input'] = $input;
    }

    /**
     * Get old input value
     *
     * @param string $key Input key
     * @param mixed $default Default value
     * @return mixed Old input value or default
     */
    public function getOldInput(string $key, $default = '')
    {
        $value = $_SESSION['old_input'][$key] ?? $default;

        // Clear old input after retrieval
        unset($_SESSION['old_input'][$key]);

        return $value;
    }

    /**
     * Clear all old input
     */
    public function clearOldInput(): void
    {
        unset($_SESSION['old_input']);
    }
}
